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
            $table->string('type'); // Loại thông báo: user, employee, admin
            $table->string('title'); // Tiêu đề thông báo
            $table->text('message'); // Nội dung thông báo
            $table->unsignedBigInteger('from_user_id')->nullable(); // Người gửi thông báo (nullable nếu là hệ thống)
            $table->unsignedBigInteger('to_user_id')->nullable(); // Người nhận thông báo
            $table->enum('status', ['read', 'unread'])->default('unread'); // Trạng thái: unread, read
            $table->timestamp('read_at')->nullable(); // Thời gian đã đọc
            $table->timestamps();
            $table->softDeletes(); // Thêm cột 'deleted_at'
            // Quan hệ khóa ngoại (nếu cần)
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('to_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
