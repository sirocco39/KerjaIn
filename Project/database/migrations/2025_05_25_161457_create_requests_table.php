<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users');
            $table->string('title', 256);
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->string('location', 512);
            $table->enum('status', ['pending', 'in progress', 'completed', 'cancelled', 'expired']);
            $table->date('work_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('requests');
    }
};
