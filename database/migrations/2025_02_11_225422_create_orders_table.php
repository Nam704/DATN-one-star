<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');
            $table->string('phone_number');
            $table->text('address');
            $table->decimal('total_amount', 10, 2);
            $table->foreignId('id_order_status')->constrained('order_statuses');
            $table->foreignId('id_voucher')->nullable()->constrained('vouchers');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
