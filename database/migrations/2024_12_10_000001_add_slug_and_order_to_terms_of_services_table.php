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
        Schema::table('terms_of_services', function (Blueprint $table) {
            $table->string('slug')->unique()->after('title_ar');
            $table->integer('order')->default(0)->after('is_active');
            
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('terms_of_services', function (Blueprint $table) {
            $table->dropIndex(['order']);
            $table->dropColumn(['slug', 'order']);
        });
    }
};
