<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('requests');
            $table->foreignId('worker_id')->constrained('users');
            $table->foreignId('requester_id')->constrained('users');
            $table->enum('status', ['accepted', 'in progress', 'submitted', 'completed', 'cancelled']);
            $table->string('order_number')->unique();
            $table->timestamp('start_work')->nullable();
            $table->timestamp('finish_work')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
