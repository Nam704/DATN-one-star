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
        Schema::create('import_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_import');
            $table->unsignedBigInteger('id_product_variant');
            $table->integer('quantity');
            $table->decimal('price_per_unit', 10, 2);
            $table->decimal('expected_price', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->timestamps();
            $table->foreign('id_import')->references('id')->on('imports')->onDelete('cascade');
            $table->foreign('id_product_variant')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_details');
    }
};
