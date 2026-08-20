<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        $paymentMethods = [
            [
                'type' => 'cash',
                'name' => 'Cash on Delivery',
                'description' => 'Pay in cash when your order arrives.',
                'icon' => null,
                'is_active' => true,
            ],
            [
                'type' => 'paymob_card',
                'name' => 'Credit or Debit Card',
                'description' => 'Pay securely online with Visa or Mastercard through Paymob.',
                'icon' => 'payment-methods/01KQ4NJ1CJMYXC43P8KA3FXNTH.jpeg',
                'is_active' => true,
            ],
            [
                'type' => 'paymob_wallet',
                'name' => 'Mobile Wallet',
                'description' => 'Pay through a supported Egyptian mobile wallet.',
                'icon' => null,
                'is_active' => true,
            ],
        ];

        foreach ($paymentMethods as $paymentMethod) {
            PaymentMethod::updateOrCreate(
                ['type' => $paymentMethod['type']],
                $paymentMethod,
            );
        }
    }
}
