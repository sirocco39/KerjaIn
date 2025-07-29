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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Pemilik wallet
            $table->decimal('amount', 12, 2); // Jumlah uang (bisa positif/negatif)
            $table->enum('type', ['credit', 'debit']); // credit = uang masuk, debit = uang keluar
            $table->string('description_id'); // Deskripsi transaksi
            $table->string('description_en');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
