<?php

namespace App\Services\Contracts;

interface PaypalServiceInterface
{
    public function createOrder(int $myOrderId, float $amount, string $currency = 'USD'): ?array;
    public function captureOrder(string $paypalOrderId): array;
    public function handleWebhook(array $payload): array;
}
