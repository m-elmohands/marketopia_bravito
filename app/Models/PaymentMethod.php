<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'description',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // type: paymob_card | paymob_wallet | paypal | cash | other
    public function isPaymob(): bool
    {
        return in_array($this->type, ['paymob_card', 'paymob_wallet']);
    }

    public function isPaypal(): bool
    {
        return $this->type === 'paypal';
    }

    public function isCash(): bool
    {
        return $this->type === 'cash';
    }
}
