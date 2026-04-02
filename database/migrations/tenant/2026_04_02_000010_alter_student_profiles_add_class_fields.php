<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            // Replace old string-based class/section columns
            $table->dropColumn(['class', 'section']);

            $table->foreignId('academic_year_id')
                  ->nullable()
                  ->after('admission_year')
                  ->constrained('academic_years')
                  ->nullOnDelete();

            $table->foreignId('class_id')
                  ->nullable()
                  ->after('academic_year_id')
                  ->constrained('classes')
                  ->nullOnDelete();

            $table->foreignId('section_id')
                  ->nullable()
                  ->after('class_id')
                  ->constrained('sections')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('student_profiles', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropForeign(['class_id']);
            $table->dropForeign(['section_id']);
            $table->dropColumn(['academic_year_id', 'class_id', 'section_id']);
            $table->string('class')->nullable();
            $table->string('section')->nullable();
        });
    }
};