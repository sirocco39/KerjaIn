<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider_name')->nullable()->after('remember_token'); // e.g., 'google', 'github'
            $table->string('provider_id')->nullable()->after('provider_name'); // Provider's unique user ID
            $table->string('provider_token')->nullable()->after('provider_id'); // Provider's access token
            $table->string('provider_refresh_token')->nullable()->after('provider_token'); // Provider's refresh token (if available)
            $table->string('avatar')->nullable()->after('provider_refresh_token'); // URL to the user's avatar

            // Make 'password' nullable because social users might not have a local password
            $table->string('password')->nullable()->change();

            // Add a unique constraint for provider_id and provider_name combination
            $table->unique(['provider_id', 'provider_name']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['provider_id', 'provider_name']); // Drop unique constraint first
            $table->dropColumn([
                'provider_name',
                'provider_id',
                'provider_token',
                'provider_refresh_token',
                'avatar',
            ]);
            // Revert password nullability if necessary (be careful with existing data)
            // $table->string('password')->nullable(false)->change();
        });
    }
};