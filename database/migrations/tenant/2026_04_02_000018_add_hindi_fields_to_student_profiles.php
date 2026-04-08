<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->string('first_name_hi')->nullable()->after('first_name');
            $table->string('last_name_hi')->nullable()->after('last_name');
            $table->string('dob_in_words_hi')->nullable()->after('dob_in_words');
            $table->string('identification_mark_hi')->nullable()->after('identification_mark');
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'first_name_hi',
                'last_name_hi',
                'dob_in_words_hi',
                'identification_mark_hi',
            ]);
        });
    }
};