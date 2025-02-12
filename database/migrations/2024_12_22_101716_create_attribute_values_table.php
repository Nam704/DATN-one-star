<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attribute_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_attribute');
            $table->string('value', 100);
            $table->string('status')->default('active');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_attribute')->references('id')->on('attributes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attribute_values');
    }
};
