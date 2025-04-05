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
        Schema::create('slide_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('slide_id');
            $table->string('image'); // Ảnh slide
            $table->foreign('slide_id')->references('id')->on('slides')->onDelete('cascade');
            $table->boolean('is_primary')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slide_images');
    }
};
