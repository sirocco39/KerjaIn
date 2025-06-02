<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests');
            $table->foreignId('offer_id')->constrained('offers');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['pending', 'paid', 'failed']);
            $table->timestamp('paid_at')->nullable();
            $table->enum('method', ['Saldo Kerjain', 'BCA VA']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payments');
    }
};
