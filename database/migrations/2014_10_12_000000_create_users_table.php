<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->string('email')->unique();
            $table->string('google_id')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('is_lock')->default('active');
            $table->string('status')->default('active');
            $table->unsignedBigInteger('id_role');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('profile_image', 250)->nullable(); // Không thay đổi do có migration update sau
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_role')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};