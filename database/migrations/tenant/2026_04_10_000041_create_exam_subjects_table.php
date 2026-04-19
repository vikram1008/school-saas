<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')
                  ->constrained('exams')
                  ->cascadeOnDelete();
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();
            $table->foreignId('section_id')
                  ->nullable()
                  ->constrained('sections')
                  ->nullOnDelete();
            $table->string('subject_name');
            $table->string('subject_name_hi')->nullable();
            $table->unsignedInteger('max_marks')->default(100);
            $table->unsignedInteger('pass_marks')->default(33);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(
                ['exam_id', 'class_id', 'section_id', 'subject_name'],
                'unique_exam_subject'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_subjects');
    }
};