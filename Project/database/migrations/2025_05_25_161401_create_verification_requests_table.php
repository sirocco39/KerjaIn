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
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
            $table->timestamp('verified_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected']);
            $table->string('first_name');
            $table->string('last_name');
            $table->char('nik', 16);
            $table->date('birthdate');
            $table->enum('gender', ['Male', 'Female']);
            $table->string('address');
            $table->char('phone_number', 12);
            $table->string('photo_url');
            $table->string('id_card_url');
            $table->string('selfie_with_id_card_url');
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('verification_requests');
    }
};
