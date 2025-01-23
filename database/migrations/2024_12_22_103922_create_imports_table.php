<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_supplier');
            $table->string('name')->unique(); // Cập nhật từ migration update
            $table->timestamp('import_date');
            $table->decimal('total_amount', 12, 2);
            $table->text('note')->nullable();
            $table->string('status')->default('pending'); // Cập nhật từ migration update
            $table->timestamps();
            $table->softDeletes(); // Cập nhật từ migration update
            $table->foreign('id_supplier')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};
