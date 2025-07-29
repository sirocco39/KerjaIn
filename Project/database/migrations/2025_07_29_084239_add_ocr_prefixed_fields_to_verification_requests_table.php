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
        Schema::table('verification_requests', function (Blueprint $table) {
            // Add new columns for OCR-extracted data, prefixed with 'ocr_'
            // Keeping only the requested fields: nik, full_name, birthdate, gender, address, raw_output
            $table->string('ocr_nik', 16)->nullable()->after('nik');
            $table->string('ocr_full_name')->nullable()->after('ocr_nik');
            $table->date('ocr_birthdate')->nullable()->after('ocr_full_name');
            $table->string('ocr_gender')->nullable()->after('ocr_birthdate');
            $table->string('ocr_address')->nullable()->after('ocr_gender'); // Unified address field
            $table->longText('ocr_raw_output')->nullable()->after('ocr_address'); // To store the entire raw OCR text
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verification_requests', function (Blueprint $table) {
            $table->dropColumn([
                'ocr_nik',
                'ocr_full_name',
                'ocr_birthdate',
                'ocr_gender',
                'ocr_address',
                'ocr_raw_output',
            ]);
        });
    }
};
