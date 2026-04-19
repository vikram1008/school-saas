<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetable_slots', function (Blueprint $table) {
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

            $table->unsignedInteger('period_number');
            $table->string('period_name');          // "Period 1", "Lunch Break"
            $table->string('start_time');           // "09:00"
            $table->string('end_time');             // "09:45"

            // NULL = applies to all days, otherwise day-specific slot
            $table->unsignedInteger('day_of_week')->nullable(); // 1=Mon...6=Sat

            $table->boolean('is_break')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(
                ['academic_year_id', 'class_id', 'section_id', 'period_number', 'day_of_week'],
                'unique_timetable_slot'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetable_slots');
    }
};