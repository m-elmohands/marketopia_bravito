<?php

namespace App\Services\Implementations;

use App\Repository\Contracts\CartRepositoryInterface;
use App\Repository\Contracts\OrderRepositoryInterface;
use App\Repository\Contracts\PaypalPaymentRepositoryInterface;
use App\Services\Contracts\PaypalServiceInterface;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaypalService implements PaypalServiceInterface
{
    protected string $clientId;
    protected string $clientSecret;
    protected string $baseUrl;
    protected Client $client;

    public function __construct(
        protected PaypalPaymentRepositoryInterface $paypalPaymentRepo,
        protected OrderRepositoryInterface $orderRepo,
        protected CartRepositoryInterface $cartRepo,
    ) {
        $this->clientId     = env('PAYPAL_CLIENT_ID');
        $this->clientSecret = env('PAYPAL_CLIENT_SECRET');
        $this->baseUrl      = env('PAYPAL_MODE', 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $this->client = new Client();
    }

    // ─── Step 1: get access token ────────────────────────────────────────────

    protected function getAccessToken(): ?string
    {
        try {
            $response = $this->client->post("{$this->baseUrl}/v1/oauth2/token", [
                'auth'        => [$this->clientId, $this->clientSecret],
                'form_params' => ['grant_type' => 'client_credentials'],
            ]);

            $data = json_decode($response->getBody(), true);
            return $data['access_token'] ?? null;
        } catch (\Exception $e) {
            Log::error('PayPal getAccessToken failed: ' . $e->getMessage());
            return null;
        }
    }

    // ─── Step 2: create PayPal order & store pending record ──────────────────

    public function createOrder(int $myOrderId, float $amount, string $currency = 'USD'): ?array
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return null;
            }

            $response = $this->client->post("{$this->baseUrl}/v2/checkout/orders", [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'intent'         => 'CAPTURE',
                    'purchase_units' => [[
                        'amount' => [
                            'currency_code' => $currency,
                            'value'         => number_format($amount, 2, '.', ''),
                        ],
                    ]],
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $approveLink = collect($data['links'] ?? [])
                ->firstWhere('rel', 'approve')['href'] ?? null;

            // persist pending record
            $this->paypalPaymentRepo->create([
                'user_id'         => Auth::id(),
                'my_order_id'     => $myOrderId,
                'paypal_order_id' => $data['id'],
                'amount'          => $amount,
                'currency'        => $currency,
                'status'          => 'pending',
            ]);

            return [
                'paypal_order_id' => $data['id'],
                'approve_url'     => $approveLink,
            ];
        } catch (\Exception $e) {
            Log::error('PayPal createOrder failed: ' . $e->getMessage());
            return null;
        }
    }

    // ─── Step 3: capture after user approves ─────────────────────────────────

    public function captureOrder(string $paypalOrderId): array
    {
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return ['success' => false, 'message' => __('message.PayPal Auth Failed')];
            }

            $response = $this->client->post(
                "{$this->baseUrl}/v2/checkout/orders/{$paypalOrderId}/capture",
                [
                    'headers' => [
                        'Authorization' => "Bearer {$token}",
                        'Content-Type'  => 'application/json',
                    ],
                ]
            );

            $data   = json_decode($response->getBody(), true);
            $status = $data['status'] ?? 'UNKNOWN'; // COMPLETED | APPROVED | VOIDED …

            $capture = $data['purchase_units'][0]['payments']['captures'][0] ?? [];

            $payer = $data['payer'] ?? [];

            $this->paypalPaymentRepo->update($paypalOrderId, [
                'paypal_transaction_id' => $capture['id'] ?? null,
                'paypal_payer_id'       => $payer['payer_id'] ?? null,
                'payer_email'           => $payer['email_address'] ?? null,
                'payer_name'            => trim(
                    ($payer['name']['given_name'] ?? '') . ' ' .
                    ($payer['name']['surname'] ?? '')
                ) ?: null,
                'status'                => strtolower($status),
            ]);

            if ($status !== 'COMPLETED') {
                return ['success' => false, 'message' => __('message.Payment Failed')];
            }

            // mark internal order as paid & clear cart
            $payment = $this->paypalPaymentRepo->getByPaypalOrderId($paypalOrderId);

            $this->orderRepo->update($payment->my_order_id, ['payment_status' => 'paid']);

            $order = $this->orderRepo->getOrderById($payment->my_order_id);
            $carts = $this->cartRepo->getUserCart($order->user_id);
            if ($carts->isNotEmpty()) {
                $carts->each->delete();
            }

            return ['success' => true, 'message' => __('message.Payment Success')];
        } catch (\Exception $e) {
            Log::error('PayPal captureOrder failed: ' . $e->getMessage());
            return ['success' => false, 'message' => __('message.Payment Failed')];
        }
    }

    // ─── Step 4: webhook (PayPal server-to-server notification) ─────────────

    public function handleWebhook(array $payload): array
    {
        try {
            $eventType = $payload['event_type'] ?? null;
            $resource  = $payload['resource'] ?? [];

            Log::info('PayPal webhook received: ' . $eventType);

            if ($eventType === 'PAYMENT.CAPTURE.COMPLETED') {
                $paypalOrderId = $resource['supplementary_data']['related_ids']['order_id']
                    ?? $resource['id']
                    ?? null;

                if (!$paypalOrderId) {
                    return ['success' => false, 'message' => 'Missing order ID'];
                }

                $payment = $this->paypalPaymentRepo->getByPaypalOrderId($paypalOrderId);

                if (!$payment) {
                    return ['success' => false, 'message' => __('message.Payment Not Found')];
                }

                $this->paypalPaymentRepo->update($paypalOrderId, [
                    'paypal_transaction_id' => $resource['id'] ?? null,
                    'status'                => 'completed',
                ]);

                $this->orderRepo->update($payment->my_order_id, ['payment_status' => 'paid']);

                $order = $this->orderRepo->getOrderById($payment->my_order_id);
                $carts = $this->cartRepo->getUserCart($order->user_id);
                if ($carts->isNotEmpty()) {
                    $carts->each->delete();
                }

                return ['success' => true, 'message' => __('message.Payment Success')];
            }

            if ($eventType === 'PAYMENT.CAPTURE.DENIED' || $eventType === 'PAYMENT.CAPTURE.REVERSED') {
                $paypalOrderId = $resource['supplementary_data']['related_ids']['order_id']
                    ?? $resource['id']
                    ?? null;

                if ($paypalOrderId) {
                    $this->paypalPaymentRepo->update($paypalOrderId, ['status' => 'failed']);
                }

                return ['success' => false, 'message' => __('message.Payment Failed')];
            }

            // unhandled event — return 200 so PayPal doesn't retry
            return ['success' => true, 'message' => 'Event ignored'];
        } catch (\Exception $e) {
            Log::error('PayPal webhook failed: ' . $e->getMessage());
            return ['success' => false, 'message' => __('message.Payment Failed')];
        }
    }
}
