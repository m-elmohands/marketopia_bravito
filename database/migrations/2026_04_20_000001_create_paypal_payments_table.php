<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paypal_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('paypal_order_id')->nullable();        // PayPal order ID returned on order creation
            $table->string('paypal_payer_id')->nullable();        // Payer ID returned after approval
            $table->string('paypal_transaction_id')->nullable();  // Capture/transaction ID after payment capture
            $table->decimal('amount', 10, 2);
            $table->string('currency', 10)->default('USD');
            $table->string('status')->default('pending');         // pending, completed, failed, refunded
            $table->string('payer_email')->nullable();
            $table->string('payer_name')->nullable();
            $table->string('payment_method')->nullable();
            $table->unsignedBigInteger('my_order_id')->nullable(); // internal order reference
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paypal_payments');
    }
};
