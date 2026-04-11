<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();
            $table->foreignId('section_id')
                  ->nullable()
                  ->constrained('sections')
                  ->nullOnDelete();
            $table->unsignedInteger('period_number');   // 1, 2, 3...
            $table->string('subject_name');
            $table->string('subject_name_hi')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->unsignedInteger('day_of_week')->nullable(); // 1=Mon...7=Sun
            $table->string('start_time')->nullable();   // "09:00"
            $table->string('end_time')->nullable();     // "09:45"
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_periods');
    }
};