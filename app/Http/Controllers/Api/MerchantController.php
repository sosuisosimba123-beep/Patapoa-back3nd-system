<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\PushNotificationService;

class MerchantController extends Controller
{
    protected $notifications;

    public function __construct(PushNotificationService $notifications)
    {
        $this->notifications = $notifications;
    }

    public function dashboard(Request $request)
    {
        $merchant = $request->user()->merchant;

        $totalOrders = Order::whereHas('orderItems', function ($query) use ($merchant) {
            $query->where('merchant_id', $merchant->id);
        })->count();

        $pendingOrders = Order::whereHas('orderItems', function ($query) use ($merchant) {
            $query->where('merchant_id', $merchant->id);
        })->whereIn('status', ['placed', 'confirmed', 'preparing'])->count();

        $completedOrders = Order::whereHas('orderItems', function ($query) use ($merchant) {
            $query->where('merchant_id', $merchant->id);
        })->where('status', 'completed')->count();

        $totalRevenue = $request->user()->transactions()
            ->where('type', 'earning')
            ->where('status', 'completed')
            ->sum('amount');

        return $this->successResponse([
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'completed_orders' => $completedOrders,
            'total_revenue' => $totalRevenue,
            'rating' => $merchant->rating,
            'is_online' => $merchant->is_online,
            'latitude' => $merchant->latitude,
            'longitude' => $merchant->longitude,
        ], 'Dashboard data retrieved successfully');
    }

    public function orders(Request $request)
    {
        $merchant = $request->user()->merchant;

        $query = Order::whereHas('orderItems', function ($query) use ($merchant) {
            $query->where('merchant_id', $merchant->id);
        })
        ->with(['orderItems', 'customer', 'address'])
        ->orderBy('created_at', 'desc');

        $orders = $this->paginateQuery($query, $request, 20, 100);

        return $this->paginatedResponse($orders, 'Merchant orders retrieved successfully');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $merchant = $request->user()->merchant;

        if (!$merchant->is_verified) {
            return $this->errorResponse('Account not verified. You cannot process orders yet.', 403);
        }

        // Verify this order belongs to this merchant
        $hasOrderItem = $order->orderItems()->where('merchant_id', $merchant->id)->exists();
        if (!$hasOrderItem) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:confirmed,preparing,ready_for_pickup',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        $validTransitions = [
            'placed' => ['confirmed'],
            'confirmed' => ['preparing'],
            'preparing' => ['ready_for_pickup'],
        ];

        if (!isset($validTransitions[$order->status]) ||
            !in_array($request->status, $validTransitions[$order->status])) {
            return $this->errorResponse('Invalid status transition', 422);
        }

        $order->update(['status' => $request->status]);

        // Update timestamp based on status
        if ($request->status === 'confirmed') {
            $order->update(['confirmed_at' => now()]);

            // Accepted orders from the merchant to the customer
            $this->notifications->sendToUser(
                $order->customer,
                'Order Accepted',
                'Your order #' . $order->id . ' has been accepted by the merchant and is being prepared.',
                ['type' => 'order_status', 'order_id' => $order->id, 'status' => 'confirmed']
            );
        } else if ($request->status === 'ready_for_pickup') {
            $this->notifications->sendToUser(
                $order->customer,
                'Order Ready',
                'Your order #' . $order->id . ' is ready for pickup!',
                ['type' => 'order_status', 'order_id' => $order->id, 'status' => 'ready_for_pickup']
            );
        }

        return $this->successResponse($order->fresh(), 'Order status updated successfully');
    }

    public function updateLocation(Request $request)
    {
        $merchant = $request->user()->merchant;

        if (!$merchant) {
            return $this->errorResponse('Merchant profile not found.', 404);
        }

        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'landmark' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:100',
            'city' => 'sometimes|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        $merchant->update($request->only(['latitude', 'longitude', 'landmark', 'district', 'city']));

        return $this->successResponse($merchant->fresh(), 'Store location updated successfully.');
    }
}
