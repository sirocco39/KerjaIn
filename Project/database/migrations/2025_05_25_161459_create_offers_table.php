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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_room_id')->constrained('chat_rooms');
            $table->foreignId('request_id')->constrained('requests');
            $table->foreignId('worker_id')->constrained('users');
            $table->foreignId('requester_id')->constrained('users');
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['open', 'closed']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('offers');
    }
};
