<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->char('phone_number', 12);
            $table->enum('role', ['user', 'admin']);
            $table->decimal('saldokerjain', 12, 2)->default(0);
            $table->boolean('is_worker')->default(false);
            $table->float('rating')->default(0);
            $table->integer('job_done')->default(0);
            $table->char('bank_acc_num', 10)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_blocked')->default(false);
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
