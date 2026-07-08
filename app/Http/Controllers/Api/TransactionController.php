<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\ClickpesaPayment;
use App\Services\ClickpesaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    protected $clickpesa;

    public function __construct(ClickpesaService $clickpesa)
    {
        $this->clickpesa = $clickpesa;
    }

    public function index(Request $request)
    {
        $query = $request->user()->transactions()
            ->with('order')
            ->orderBy('created_at', 'desc');

        $transactions = $this->paginateQuery($query, $request, 20, 100);

        return $this->paginatedResponse($transactions, 'Transactions retrieved successfully');
    }

    public function initiatePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:mpesa,tigo_pesa,airtel_money,halopesa,card,wallet',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        $order = Order::findOrFail($request->order_id);

        if ($order->customer_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        if ($order->payment_status === 'paid') {
            return $this->errorResponse('Order already paid', 422);
        }

        try {
            DB::beginTransaction();

            if ($request->payment_method === 'wallet') {
                return $this->processWalletPayment($request->user(), $order);
            } else {
                // Clickpesa Integration
                $transRef = 'PAT-' . time() . '-' . $order->id;

                $transaction = Transaction::create([
                    'user_id' => $request->user()->id,
                    'order_id' => $order->id,
                    'type' => 'payment',
                    'status' => 'pending',
                    'amount' => $order->total,
                    'currency' => 'TZS',
                    'payment_method' => $request->payment_method,
                    'description' => 'Payment for order #' . $order->id,
                    'transaction_reference' => $transRef,
                ]);

                $response = null;
                $instruction = 'Follow the instructions on your phone to complete payment.';
                $paymentUrl = null;

                if ($request->payment_method === 'card') {
                    $response = $this->clickpesa->initiateCardPayment([
                        'amount' => $order->total,
                        'reference' => $transRef,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                    ]);
                    $paymentUrl = $response['checkout_url'] ?? null;
                    $instruction = 'Please complete the payment on the opened secure page.';
                } else {
                    // Mobile Money (USSD Push)
                    $response = $this->clickpesa->initiateUSSD([
                        'amount' => $order->total,
                        'reference' => $transRef,
                        'phone' => $request->user()->phone,
                    ]);
                }

                DB::commit();

                return $this->successResponse([
                    'transaction' => $transaction,
                    'order' => $order->fresh(),
                    'instruction' => $instruction,
                    'payment_url' => $paymentUrl,
                    'gateway_raw' => $response,
                ], 'Payment initiated via Clickpesa');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Initiation Error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Failed to create payment: ' . $e->getMessage(), 500);
        }
    }

    protected function processWalletPayment($user, $order)
    {
        $wallet = $user->wallet;

        if (!$wallet || $wallet->balance < $order->total) {
            DB::rollBack();
            return $this->errorResponse('Insufficient wallet balance', 422);
        }

        $wallet->decrement('balance', $order->total);

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'order_id' => $order->id,
            'type' => 'payment',
            'status' => 'completed',
            'amount' => $order->total,
            'currency' => 'TZS',
            'payment_method' => 'wallet',
            'description' => 'Wallet payment for order #' . $order->id,
            'processed_at' => now(),
        ]);

        $order->update([
            'payment_status' => 'paid',
            'payment_reference' => $transaction->id,
            'status' => 'confirmed',
        ]);

        DB::commit();

        return $this->successResponse([
            'transaction' => $transaction,
            'order' => $order->fresh(),
        ], 'Payment processed successfully');
    }

    public function paymentCallback(Request $request)
    {
        $data = $request->all();
        Log::info('Clickpesa Webhook Received', ['data' => $data]);

        // Transaction reference from Clickpesa payload (reference_id in our local table)
        $externalId = $data['transaction_id'] ?? null;

        $cpPayment = ClickpesaPayment::where('external_id', $externalId)
            ->orWhere('reference_id', $data['orderReference'] ?? '')
            ->first();

        if (!$cpPayment) {
            return response()->json(['status' => 'error', 'message' => 'Payment record not found'], 404);
        }

        $transaction = Transaction::where('transaction_reference', $cpPayment->reference_id)->first();

        if (!$transaction) {
            return response()->json(['status' => 'error', 'message' => 'Platform transaction not found'], 404);
        }

        try {
            DB::beginTransaction();

            $status = strtoupper($data['status'] ?? '');

            // Map Clickpesa status to our internal status
            $internalStatus = 'processing';
            if ($status === 'SUCCESSFUL' || $status === 'PAID') {
                $internalStatus = 'successful';

                $transaction->update([
                    'status' => 'completed',
                    'processed_at' => now(),
                ]);

                $order = $transaction->order;
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'confirmed',
                ]);

            } else if (in_array($status, ['FAILED', 'CANCELLED', 'DECLINED'])) {
                $internalStatus = 'failed';

                $transaction->update([
                    'status' => 'failed',
                    'processed_at' => now(),
                ]);

                $transaction->order->update(['payment_status' => 'failed']);
            }

            $cpPayment->update([
                'status' => $internalStatus,
                'status_detail' => $data['message'] ?? $status,
                'response_payload' => array_merge($cpPayment->response_payload ?? [], ['callback' => $data]),
                'paid_at' => $internalStatus === 'successful' ? now() : null,
            ]);

            DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Callback processed']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Clickpesa Callback Error', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'Internal error'], 500);
        }
    }

    /**
     * Merchant and Rider Payout Request
     */
    public function payoutRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:5000',
            'phone' => 'required|string',
            'provider' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        $user = $request->user();

        // Find the appropriate wallet based on user type
        $wallet = Wallet::where('user_id', $user->id)
            ->where('wallet_type', $user->user_type)
            ->first();

        if (!$wallet || $wallet->balance < $request->amount) {
            return $this->errorResponse('Insufficient balance in your ' . $user->user_type . ' wallet', 422);
        }

        try {
            DB::beginTransaction();

            $transRef = 'PO-' . strtoupper($user->user_type[0]) . '-' . time() . '-' . $user->id;

            // Debit wallet
            $wallet->decrement('balance', $request->amount);

            // Create platform transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'type' => 'payout',
                'status' => 'pending',
                'amount' => $request->amount,
                'currency' => 'TZS',
                'payment_method' => $request->provider,
                'description' => 'Payout from ' . $user->user_type . ' wallet to ' . $request->phone,
                'transaction_reference' => $transRef,
            ]);

            // Call Clickpesa Disbursement
            $response = $this->clickpesa->payout([
                'amount' => $request->amount,
                'phone' => $request->phone,
                'reference' => $transRef,
                'description' => 'Patapoa Payout for ' . $user->name,
            ]);

            DB::commit();

            return $this->successResponse([
                'transaction' => $transaction,
                'gateway_response' => $response,
            ], 'Payout request submitted to Clickpesa');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payout Error', ['error' => $e->getMessage()]);
            return $this->errorResponse('Failed to process payout: ' . $e->getMessage(), 500);
        }
    }

    public function checkStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        if ($order->customer_id !== $request->user()->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse([
            'order_id' => $order->id,
            'payment_status' => $order->payment_status,
            'status' => $order->status,
        ]);
    }
}
