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
         Schema::create('order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_order'); // khóa ngoại đến bảng orders
            $table->unsignedBigInteger('id_product_variant'); // khóa ngoại đến bảng product_variants
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->unsignedBigInteger('id_user');
            $table->timestamps();

            $table->foreign('id_order')->references('id')->on('orders');
            $table->foreign('id_product_variant')->references('id')->on('product_variants');
            $table->foreign('id_user')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
