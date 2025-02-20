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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('txn_ref')->nullable();          // Mã đơn hàng từ VNPay
            $table->decimal('amount', 12, 2)->nullable();      // Số tiền thanh toán (đã chia 100 nếu cần)
            $table->string('bank_code')->nullable();           // Mã ngân hàng
            $table->string('transaction_no')->nullable();      // Số giao dịch VNPay
            $table->string('card_type')->nullable();           // Loại thẻ (ATM, Credit,…)
            $table->string('response_code')->nullable();       // Mã phản hồi (00 = thành công)
            $table->string('pay_date')->nullable();            // Thời gian thanh toán
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
