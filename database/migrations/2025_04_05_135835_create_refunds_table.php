<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->json('bank_details'); // Lưu thông tin ngân hàng dạng JSON
            $table->string('status')->default('pending'); // pending/approved/rejected/completed
            $table->text('staff_notes')->nullable(); // Ghi chú của nhân viên
            $table->timestamp('refunded_at')->nullable(); // Thời gian hoàn tiền
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refunds');
    }
};
