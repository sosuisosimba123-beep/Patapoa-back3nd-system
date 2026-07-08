<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\ClickpesaPayment;

class ClickpesaService
{
    protected $clientId;
    protected $clientSecret;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.clickpesa.client_id');
        $this->clientSecret = config('services.clickpesa.client_secret');
        $this->baseUrl = rtrim(config('services.clickpesa.base_url'), '/');
    }

    /**
     * Generate Access Token
     */
    public function getAccessToken()
    {
        return Cache::remember('clickpesa_oauth_token', 55 * 60, function () {
            $url = "{$this->baseUrl}/third-parties/generate-token";

            $response = Http::withHeaders([
                'client-id' => $this->clientId,
                'api-key' => $this->clientSecret,
                'Accept' => 'application/json',
            ])->post($url);

            if ($response->failed()) {
                Log::error('Clickpesa Token Generation Failed', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                throw new \Exception('Unable to authenticate with ClickPesa: ' . $response->body());
            }

            $data = $response->json();
            $token = $data['token'] ?? $data['access_token'] ?? null;

            if (!$token) {
                throw new \Exception('ClickPesa response missing token: ' . json_encode($data));
            }

            return $token;
        });
    }

    /**
     * Initiate USSD Checkout (USSD Push)
     */
    public function initiateUSSD(array $params)
    {
        $token = $this->getAccessToken();

        $payload = [
            'amount' => (string) $params['amount'],
            'currency' => $params['currency'] ?? 'TZS',
            'phoneNumber' => $params['phone'],
            'orderReference' => (string) $params['reference'],
        ];

        // Track in DB before sending
        $payment = ClickpesaPayment::create([
            'reference_id' => $params['reference'],
            'payment_method' => 'ussd',
            'phone_number' => $params['phone'],
            'amount' => $params['amount'],
            'currency' => $payload['currency'],
            'request_payload' => $payload,
            'status' => 'pending',
        ]);

        $url = "{$this->baseUrl}/third-parties/payments/initiate-ussd-push-request";

        $response = Http::withToken($token)
            ->withHeaders([
                'client-id' => $this->clientId,
                'Accept' => 'application/json',
            ])
            ->post($url, $payload);

        if ($response->failed()) {
            throw new \Exception('ClickPesa USSD Push Failed: ' . $response->status() . ' - ' . $response->body());
        }

        $responseData = $response->json();

        $payment->update([
            'response_payload' => $responseData,
            'external_id' => $responseData['transaction_id'] ?? null,
            'status' => $response->successful() ? 'processing' : 'failed',
            'status_detail' => $responseData['message'] ?? ($response->successful() ? 'Processing' : 'Initiation failed'),
        ]);

        return $responseData;
    }

    /**
     * Initiate Card Payment
     */
    public function initiateCardPayment(array $params)
    {
        $token = $this->getAccessToken();

        // Note: Re-verify card endpoint if needed, often similar pattern
        $payload = [
            'amount' => (string) $params['amount'],
            'currency' => $params['currency'] ?? 'TZS',
            'orderReference' => (string) $params['reference'],
            'callbackUrl' => $params['callback_url'] ?? route('payments.callback'),
            'description' => $params['description'] ?? 'Card Payment',
            'customerEmail' => $params['email'] ?? 'customer@patapoa.com',
            'customerName' => $params['name'] ?? 'Customer',
        ];

        $payment = ClickpesaPayment::create([
            'reference_id' => $params['reference'],
            'payment_method' => 'card',
            'amount' => $params['amount'],
            'currency' => $payload['currency'],
            'request_payload' => $payload,
            'status' => 'pending',
        ]);

        $url = "{$this->baseUrl}/third-parties/payments/initiate-card-payment"; // Guessing based on pattern

        $response = Http::withToken($token)
            ->post($url, $payload);

        $responseData = $response->json();

        $payment->update([
            'response_payload' => $responseData,
            'external_id' => $responseData['transaction_id'] ?? null,
            'status' => $response->successful() ? 'processing' : 'failed',
            'status_detail' => $responseData['message'] ?? ($response->successful() ? 'Redirecting' : 'Failed'),
        ]);

        return $responseData;
    }

    /**
     * Query Status of a transaction
     */
    public function queryStatus(string $reference)
    {
        $token = $this->getAccessToken();
        $url = "{$this->baseUrl}/third-parties/payments/query-payment-status?orderReference=" . urlencode($reference);

        $response = Http::withToken($token)->get($url);

        return $response->json();
    }

    /**
     * Payout (Disbursement) to Mobile Money
     */
    public function payout(array $params)
    {
        $token = $this->getAccessToken();

        $payload = [
            'amount' => (string) $params['amount'],
            'currency' => $params['currency'] ?? 'TZS',
            'phoneNumber' => $params['phone'],
            'orderReference' => (string) $params['reference'],
        ];

        $payment = ClickpesaPayment::create([
            'reference_id' => $params['reference'],
            'payment_method' => 'payout',
            'phone_number' => $params['phone'],
            'amount' => $params['amount'],
            'status' => 'pending',
            'request_payload' => $payload,
        ]);

        try {
            $url = "{$this->baseUrl}/third-parties/payouts/create-mobile-money-payout";

            $response = Http::withToken($token)
                ->post($url, $payload);

            $responseData = $response->json();

            $payment->update([
                'response_payload' => $responseData,
                'external_id' => $responseData['transaction_id'] ?? null,
                'status' => $response->successful() ? 'processing' : 'failed',
                'status_detail' => $responseData['message'] ?? ($response->successful() ? 'Payout initiated' : 'Payout failed'),
            ]);

            return $responseData;
        } catch (\Exception $e) {
            $payment->update(['status' => 'failed', 'status_detail' => $e->getMessage()]);
            throw $e;
        }
    }
}
