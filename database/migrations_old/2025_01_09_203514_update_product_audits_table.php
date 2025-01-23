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
        Schema::table('product_audits', function (Blueprint $table) {
            $table->enum('action_type', ['add', 'remote', 'update', 'import'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_audits', function (Blueprint $table) {
            $table->enum('action_type', ['add', 'remote', 'update'])->change();
        });
    }
};
