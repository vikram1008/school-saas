<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_subjects', function (Blueprint $table) {
            $table->string('subject_1_hi')->nullable()->after('subject_1');
            $table->string('subject_2_hi')->nullable()->after('subject_2');
            $table->string('subject_3_hi')->nullable()->after('subject_3');
            $table->string('subject_4_hi')->nullable()->after('subject_4');
            $table->string('subject_5_hi')->nullable()->after('subject_5');
            $table->string('additional_subject_hi')->nullable()->after('additional_subject');
        });
    }

    public function down(): void
    {
        Schema::table('student_subjects', function (Blueprint $table) {
            $table->dropColumn([
                'subject_1_hi',
                'subject_2_hi',
                'subject_3_hi',
                'subject_4_hi',
                'subject_5_hi',
                'additional_subject_hi',
            ]);
        });
    }
};