<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')
                  ->constrained('exams')
                  ->cascadeOnDelete();
            $table->foreignId('student_profile_id')
                  ->constrained('student_profiles')
                  ->cascadeOnDelete();
            $table->foreignId('exam_subject_id')
                  ->constrained('exam_subjects')
                  ->cascadeOnDelete();
            $table->decimal('marks_obtained', 6, 2)->default(0);
            $table->boolean('is_absent')->default(false);
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(
                ['exam_id', 'student_profile_id', 'exam_subject_id'],
                'unique_student_mark'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_marks');
    }
};