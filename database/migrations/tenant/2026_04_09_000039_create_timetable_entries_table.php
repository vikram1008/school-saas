<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();
            $table->foreignId('section_id')
                  ->nullable()
                  ->constrained('sections')
                  ->nullOnDelete();

            $table->unsignedInteger('day_of_week');    // 1=Mon...6=Sat
            $table->unsignedInteger('period_number');

            $table->string('subject_name');
            $table->string('subject_name_hi')->nullable();

            $table->foreignId('teacher_id')
                  ->nullable()
                  ->constrained('staff_profiles')
                  ->nullOnDelete();

            $table->string('room_number')->nullable();
            $table->timestamps();

            $table->unique(
                ['academic_year_id', 'class_id', 'section_id', 'day_of_week', 'period_number'],
                'unique_timetable_entry'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_entries');
    }
};