<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_product');
            $table->string('sku', 100)->unique();
            $table->string('status')->default('active');
            $table->unsignedInteger('quantity')->default(0);
            $table->decimal('price', 10, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_product')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};