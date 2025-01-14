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
        Schema::create('product_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->unsignedBigInteger('id_product_variant');
            $table->integer('quantity')->nullable();
            $table->enum('action_type', ['add', 'remote', 'update']);
            $table->text('reason')->nullable();
            $table->timestamps();
            // Khóa ngoại
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('id_product_variant')->references('id')->on('product_variants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_audits');
    }
};
