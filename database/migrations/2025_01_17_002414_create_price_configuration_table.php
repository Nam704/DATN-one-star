<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_configuration', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_variant_id');
            $table->string('use_price_from')->default('variant');
            $table->timestamps();

            $table->foreign('product_variant_id')
                ->references('id')
                ->on('product_variants')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_configuration');
    }
};
