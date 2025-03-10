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
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->string('type'); // Loại giảm giá
            $table->decimal('discount_amount', 10, 2);
            $table->decimal('max_discount_amount', 10, 2)->nullable(); // Giới hạn giảm giá tối đa
            $table->integer('quantity');
            $table->integer('user_limit')->default(1); // Số lần tối đa cho mỗi user
            $table->integer('total_usage')->default(0); // Tổng số lần đã sử dụng
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->decimal('min_amount', 10, 2);
            $table->string('status')->default('active'); // Trạng thái
            $table->string('applies_to')->nullable(); // Áp dụng cho danh mục/sản phẩm cụ thể
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
