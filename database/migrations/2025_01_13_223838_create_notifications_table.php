<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('category')->nullable(); // Phân loại thông báo (payment, order, promotion, system, etc.)
            $table->string('priority')->default('medium'); // Mức độ ưu tiên: high, medium, low
            $table->string('title');
            $table->text('message');
            $table->unsignedBigInteger('from_user_id')->nullable();
            $table->unsignedBigInteger('to_user_id')->nullable();
            $table->unsignedBigInteger('goto_id')->nullable();
            $table->string('goto_route')->nullable(); // Route để chuyển hướng (e.g., client.order.detail)
            $table->string('status')->default('unread');
            $table->timestamp('read_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // Thời hạn thông báo
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
