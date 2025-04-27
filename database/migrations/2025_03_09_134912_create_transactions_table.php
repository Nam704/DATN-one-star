<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('txn_ref')->index(); // Bỏ unique, thêm index để tìm kiếm nhanh
            $table->decimal('amount', 15, 2);
            $table->string('status');
            $table->string('payment_method')->nullable(); // Thêm để biết cổng thanh toán
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
