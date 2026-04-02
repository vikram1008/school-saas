<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section_subject_teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();

            // Nullable — null means single-tier (class-level assignment)
            $table->foreignId('section_id')
                  ->nullable()
                  ->constrained('sections')
                  ->cascadeOnDelete();

            $table->unsignedBigInteger('user_id');   // teacher user_id
            $table->string('subject_name');          // "Mathematics", "Science"
            $table->timestamps();

            // One teacher per subject per section
            $table->unique(['class_id', 'section_id', 'subject_name'], 'unique_subject_per_section');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section_subject_teachers');
    }
};