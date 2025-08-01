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
            $table->foreignId('user_id')->constrained('users'); 
            $table->decimal('amount', 12, 2); 
            $table->enum('status', ['pending', 'paid', 'expired']); 
            $table->string('external_id')->nullable(); 
            $table->string('xendit_invoice_id')->nullable(); 
            $table->text('invoice_url')->nullable(); 
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
