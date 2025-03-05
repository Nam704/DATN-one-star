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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('user_name');
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->foreignId('id_address')->constrained('addresses')->onDelete('cascade');
            $table->decimal('total', 15, 2);
            $table->foreignId('id_order_status')->constrained('order_statuses');
            $table->foreignId('id_voucher')->nullable()->constrained('vouchers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
