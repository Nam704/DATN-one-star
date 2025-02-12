<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_product_variant');
            $table->unsignedBigInteger('id_attribute_value');
            $table->timestamps();
            $table->foreign('id_product_variant')->references('id')->on('product_variants')->onDelete('cascade');
            $table->foreign('id_attribute_value')->references('id')->on('attribute_values')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variant_attributes');
    }
};
