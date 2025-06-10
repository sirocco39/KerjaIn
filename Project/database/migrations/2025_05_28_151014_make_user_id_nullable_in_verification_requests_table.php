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
        Schema::table('verification_requests', function (Blueprint $table) {
            // Pastikan tipe kolom di sini cocok dengan yang ada di tabel Anda
            // Jika Anda pakai foreignId (recommended):
            $table->foreignId('user_id')->nullable()->change();
            // Jika Anda pakai unsignedBigInteger:
            // $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_requests', function (Blueprint $table) {
            // Untuk mengembalikan ke NOT NULL, Anda perlu memastikan tidak ada NULLs
            // Jika ada NULL values, ini akan gagal.
            // $table->foreignId('user_id')->nullable(false)->change();
            // Atau lebih baik:
            // $table->foreignId('user_id')->change(); // Ini akan mengembalikan default Laravel (NOT NULL)
        });
    }
};