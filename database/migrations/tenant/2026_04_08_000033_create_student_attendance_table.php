<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')
                  ->constrained('student_profiles')
                  ->cascadeOnDelete();
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();
            $table->foreignId('section_id')
                  ->nullable()
                  ->constrained('sections')
                  ->nullOnDelete();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();

            $table->date('date');
            $table->enum('status', [
                'present',
                'absent',
                'late',
                'half_day',
                'leave',
            ])->default('present');

            $table->enum('attendance_type', [
                'class_wise',
                'subject_wise',
            ])->default('class_wise');

            // For subject-wise
            $table->foreignId('period_id')
                  ->nullable()
                  ->constrained('attendance_periods')
                  ->nullOnDelete();
            $table->string('subject_name')->nullable();

            $table->unsignedBigInteger('marked_by');
            $table->string('remarks')->nullable();
            $table->timestamps();

            // Prevent duplicate records
            $table->unique(
                ['student_profile_id', 'date', 'attendance_type', 'period_id'],
                'unique_student_attendance'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_attendance');
    }
};