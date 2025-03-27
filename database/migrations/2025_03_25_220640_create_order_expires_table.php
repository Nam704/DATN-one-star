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
        Schema::create('order_expires', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_order');
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->foreign('id_order')->references('id')->on('orders')->onDelete('cascade'); // Thêm onDelete('cascade') để xóa bản ghi khi order bị xóa.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_expires');
    }
};
