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
        Schema::create('slides', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Tiêu đề slide
            $table->text('description')->nullable(); // Mô tả
            $table->unsignedBigInteger('category_id')->nullable(); // Danh mục sản phẩm (Nếu muốn hiển thị theo danh mục)
            $table->boolean('is_active')->default(0); // Trạng thái hiển thị (1)
            $table->json('display_locations')->nullable(); // Chứa danh sách vị trí hiển thị
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slides');
    }
};
