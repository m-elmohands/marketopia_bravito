<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\PaymobPaymentCallBackRequest;
use App\Models\PaymentMethod;
use App\Services\Contracts\PaymobServiceInterface;
use App\Services\Contracts\PaypalServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymobServiceInterface $paymobService,
        protected PaypalServiceInterface $paypalService,
    ) {}

    // ─── Paymob ──────────────────────────────────────────────────────────────

    public function callback(PaymobPaymentCallBackRequest $request)
    {
        $result = $this->paymobService->paymentCallback($request->validated());

        if (! $result['success']) {
            return Response::api($result['message'], 400, false, 400);
        }

        return Response::api($result['message'], 200, true, 200);
    }

    /**
     * Handle Paymob's browser response callback.
     *
     * This route only redirects the customer. Payment state is updated solely
     * by the HMAC-verified processed callback above.
     */
    public function paymobResponse(Request $request)
    {
        $parameters = http_build_query([
            'provider' => 'paymob',
            'success' => filter_var($request->query('success'), FILTER_VALIDATE_BOOLEAN) ? '1' : '0',
            'pending' => filter_var($request->query('pending'), FILTER_VALIDATE_BOOLEAN) ? '1' : '0',
            'transaction_id' => $request->query('id'),
            'order_id' => $request->query('order'),
        ]);

        $frontendUrl = rtrim((string) config('app.frontend_url'), '/');

        return redirect()->away("{$frontendUrl}/payment/result?{$parameters}");
    }

    // ─── PayPal ──────────────────────────────────────────────────────────────

    /**
     * Create a PayPal order and return the approval URL.
     *
     * POST /api/paypal/create-order
     * Body: { "order_id": 1, "amount": 99.99, "currency": "USD" }
     */
    public function paypalCreateOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'sometimes|string|size:3',
        ]);

        // Log PayPal environment variables
        logger()->info('PayPal ENV Values', [
            'client_id' => config('services.paypal.client_id'),
            'secret' => config('services.paypal.secret'),
            'mode' => config('services.paypal.mode'),
        ]);

        logger()->info('PayPal Create Order Request', [
            'order_id' => $request->order_id,
            'amount' => $request->amount,
            'currency' => $request->currency ?? 'USD',
        ]);
        $result = $this->paypalService->createOrder(
            $request->order_id,
            $request->amount,
            $request->currency ?? 'USD',
        );

        if (! $result) {
            return Response::api(__('message.Payment Failed'), 500, false, 500);
        }

        return Response::api(__('message.Order Created'), 200, true, 200, $result);
    }

    /**
     * Capture the payment after the user approves on PayPal.
     *
     * POST /api/paypal/capture
     * Body: { "paypal_order_id": "PAYPAL-ORDER-ID" }
     */
    public function paypalCapture(Request $request)
    {
        $request->validate([
            'paypal_order_id' => 'required|string',
        ]);

        $result = $this->paypalService->captureOrder($request->paypal_order_id);

        if (! $result['success']) {
            return Response::api($result['message'], 400, false, 400);
        }

        return Response::api($result['message'], 200, true, 200);
    }

    /**
     * PayPal webhook (server-to-server callback).
     *
     * POST /api/paypal/webhook
     */
    public function paypalWebhook(Request $request)
    {
        $result = $this->paypalService->handleWebhook($request->all());

        // always return 200 so PayPal doesn't keep retrying
        return Response::api($result['message'], 200, $result['success'], 200);
    }

    public function payment_method()
    {
        $payment_methods = PaymentMethod::where('is_active', true)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'description' => $item->description,
                    'icon' => url('storage/'.$item->icon),
                ];
            });

        return response()->json([
            'payment_methods' => $payment_methods,
        ]);
    }
}
