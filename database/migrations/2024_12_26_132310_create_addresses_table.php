<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->text('address_detail', 500);
            $table->boolean('is_default')->default(false);
            $table->unsignedBigInteger('id_ward');
            $table->morphs('addressable');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_ward')->references('id')->on('wards')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
