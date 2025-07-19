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
        Schema::create('top_up_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users'); // Pemilik akun yang melakukan top up
            $table->decimal('amount', 12, 2); // Jumlah uang yang di-top up
            $table->enum('status', ['pending', 'paid', 'expired']); // Status top up// Metode pembayaran (
            $table->string('external_id')->nullable(); // ID eksternal dari sistem pembayaran (jika ada)
            $table->string('xendit_invoice_id')->nullable(); // ID invoice dari Xendit (jika menggunakan Xendit
            $table->text('invoice_url')->nullable(); // URL invoice untuk pembayaran\
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('top_up_orders');
    }
};
