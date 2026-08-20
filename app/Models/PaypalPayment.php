<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaypalPayment extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'paypal_order_id',
        'paypal_payer_id',
        'paypal_transaction_id',
        'amount',
        'currency',
        'status',
        'payer_email',
        'payer_name',
        'payment_method',
        'my_order_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'my_order_id');
    }
}
