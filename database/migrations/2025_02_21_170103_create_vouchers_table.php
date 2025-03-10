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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 250);
            $table->string('code', 250);
            $table->string('description', 250)->nullable();
            $table->decimal('discount_amount', 12, 2);
            $table->bigInteger('quantity');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->bigInteger('min_amount');
            $table->decimal('maximum_value', 12, 2);
            $table->string('discount_type', 250);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
