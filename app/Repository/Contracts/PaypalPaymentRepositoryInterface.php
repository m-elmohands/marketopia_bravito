<?php

namespace App\Repository\Contracts;

interface PaypalPaymentRepositoryInterface
{
    public function create(array $data);
    public function getByPaypalOrderId(string $paypalOrderId);
    public function update(string $paypalOrderId, array $data);
}
