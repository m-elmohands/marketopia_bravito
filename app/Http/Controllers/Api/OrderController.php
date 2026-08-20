<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FilterOrderRequest;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Models\Cart;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\Contracts\OrderServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderServiceInterface $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(FilterOrderRequest $request)
    {
        $result = $this->orderService->getOrders(
            auth('api')->user()->id,
            $request->per_page ?? 10,
            $request->status ?? null,
            $request->payment_status ?? null,
            $request->payment_method ?? null,
            $request->search ?? null
        );

        if (! $result['success']) {
            return Response::api($result['message'], 400, false, 400);
        }

        return Response::api($result['message'], 200, true, 200, $result['data']);
    }

    public function store(StoreOrderRequest $request)
    {
        $paymentMethod = null;
        if ($request->payment_method_id) {
            $paymentMethodModel = PaymentMethod::find($request->payment_method_id);
            $paymentMethod = match ($paymentMethodModel?->type) {
                'paymob_card' => 'visa',
                'paymob_wallet' => 'wallet',
                'cash' => 'cash',
                default => null,
            };
        }
        $result = $this->orderService->create([
            'address_id' => $request->address_id,
            'area_id' => $request->area_id,
            'user_id' => auth('api')->user()->id,
            'coupon_id' => $request->coupon_id,
            'payment_method_id' => $request->payment_method_id,
            'payment_method' => $paymentMethod,
            'notes' => $request->notes,
            'wallet_number' => $request->wallet_number ?? null,
        ]);

        if (! $result['success']) {
            return Response::api($result['message'], 400, false, 400);
        }

        Cart::where('user_id', auth('api')->user()->id)->delete();

        return Response::api($result['message'], 200, true, 200, $result['data']);
    }

    public function show(int $id)
    {
        $result = $this->orderService->getOrderById($id);

        if (! $result['success']) {
            return Response::api($result['message'], 400, false, 400);
        }

        return Response::api($result['message'], 200, true, 200, $result['data']);
    }

    public function uploadReceipt(int $id, Request $request)
    {
        $request->validate([
            'receipt_image' => 'required|image|max:5120',
        ]);

        $order = Order::where('id', $id)
            ->where('user_id', auth('api')->user()->id)
            ->first();

        if (! $order) {
            return Response::api(__('message.Order Not Found'), 404, false, 404);
        }

        $path = $request->file('receipt_image')->store('receipts', 'public');

        $order->update([
            'receipt_image' => $path,
            'payment_status' => 'paid',
        ]);

        return Response::api(__('message.Success'), 200, true, 200, [
            'receipt_image' => asset('storage/'.$path),
        ]);
    }
}
