<?php

namespace App\Repository\Eloquent;

use App\Models\PaypalPayment;
use App\Repository\Contracts\PaypalPaymentRepositoryInterface;

class PaypalPaymentRepository implements PaypalPaymentRepositoryInterface
{
    public function create(array $data)
    {
        return PaypalPayment::create($data);
    }

    public function getByPaypalOrderId(string $paypalOrderId)
    {
        return PaypalPayment::where('paypal_order_id', $paypalOrderId)->first();
    }

    public function update(string $paypalOrderId, array $data)
    {
        $payment = PaypalPayment::where('paypal_order_id', $paypalOrderId)->first();
        $payment->update($data);
        return $payment;
    }
}
