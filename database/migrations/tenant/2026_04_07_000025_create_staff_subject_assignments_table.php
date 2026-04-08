<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_subject_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_profile_id')
                  ->constrained('staff_profiles')
                  ->cascadeOnDelete();

            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();

            $table->foreignId('section_id')
                  ->nullable()
                  ->constrained('sections')
                  ->cascadeOnDelete();

            $table->string('subject_name');
            $table->string('subject_name_hi')->nullable();

            $table->timestamps();

            $table->unique(
                ['staff_profile_id', 'class_id', 'section_id', 'subject_name'],
                'unique_staff_subject'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_subject_assignments');
    }
};