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
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_user')->nullable(); 
            $table->string('phone_number', 20);
            $table->unsignedBigInteger('id_address');
            $table->decimal('total_amount', 12, 2);
            $table->unsignedBigInteger('id_order_status');
            $table->unsignedBigInteger('id_voucher');
            $table->string('email', 250)->nullable();
            $table->timestamps();

            $table->foreign('id_user')->references('id')->on('users');
            $table->foreign('id_address')->references('id')->on('addresses');
            $table->foreign('id_order_status')->references('id')->on('order_statuses');
            $table->foreign('id_voucher')->references('id')->on('vouchers');
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
