<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            // Office Use
            $table->string('sr_number')->nullable()->unique()->after('admission_number');
            $table->date('admission_date')->nullable()->after('sr_number');

            // Personal
            $table->string('dob_in_words')->nullable()->after('date_of_birth');
            $table->string('aadhaar_number', 12)->nullable()->after('dob_in_words');
            $table->string('jan_aadhaar_number')->nullable()->after('aadhaar_number');
            $table->enum('category', [
                'general', 'sc', 'st', 'obc', 'mbc', 'ews'
            ])->nullable()->after('jan_aadhaar_number');
            $table->boolean('minority_status')->default(false)->after('category');
            $table->boolean('bpl_status')->default(false)->after('minority_status');
            $table->string('cwsn_type')->nullable()->after('bpl_status'); // disability type
            $table->string('identification_mark')->nullable()->after('cwsn_type');

            // Contact
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('email')->nullable()->after('whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'sr_number', 'admission_date', 'dob_in_words',
                'aadhaar_number', 'jan_aadhaar_number', 'category',
                'minority_status', 'bpl_status', 'cwsn_type',
                'identification_mark', 'whatsapp', 'email',
            ]);
        });
    }
};