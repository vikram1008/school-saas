<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')
                  ->unique()
                  ->constrained('student_profiles')
                  ->cascadeOnDelete();

            $table->enum('stream', ['arts', 'science', 'commerce', 'agriculture', 'na'])
                  ->default('na');
            $table->string('subject_1')->nullable();
            $table->string('subject_2')->nullable();
            $table->string('subject_3')->nullable();
            $table->string('subject_4')->nullable();
            $table->string('subject_5')->nullable();
            $table->string('additional_subject')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_subjects');
    }
};