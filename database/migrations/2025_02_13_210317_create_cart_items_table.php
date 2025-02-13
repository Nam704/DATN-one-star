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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id(); // ID chính của cart item
            $table->unsignedBigInteger('id_cart'); // Liên kết với bảng carts
            $table->unsignedBigInteger('id_product_variant'); // Liên kết với product_variants
            $table->integer('quantity'); // Số lượng sản phẩm
            $table->decimal('price', 10, 2); // Giá sản phẩm tại thời điểm thêm vào giỏ
            $table->timestamps();

            // Khóa ngoại
            $table->foreign('id_cart')->references('id')->on('carts')->onDelete('cascade');
            $table->foreign('id_product_variant')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
