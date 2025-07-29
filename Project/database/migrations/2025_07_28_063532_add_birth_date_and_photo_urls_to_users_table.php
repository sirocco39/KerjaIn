<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->date('birth_date')->nullable()->after('phone_number');
            $table->string('photo_url_user')->nullable()->after('birth_date');
            $table->string('photo_url_worker')->nullable()->after('photo_url_user');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'photo_url_user', 'photo_url_worker']);
        });
    }
};
