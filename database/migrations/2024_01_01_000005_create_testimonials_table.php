<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('client_name_en');
            $table->string('client_name_ar');
            $table->string('client_position_en')->nullable();
            $table->string('client_position_ar')->nullable();
            $table->string('client_company_en')->nullable();
            $table->string('client_company_ar')->nullable();
            $table->string('client_image')->nullable();
            $table->text('testimonial_en');
            $table->text('testimonial_ar');
            $table->integer('rating')->default(5);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('rating');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
