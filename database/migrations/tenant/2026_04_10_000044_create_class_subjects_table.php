<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();
            $table->string('subject_name');
            $table->string('subject_name_hi')->nullable();
            $table->unsignedBigInteger('teacher_id')->nullable(); // staff_profile_id
            $table->unsignedInteger('periods_per_week')->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['class_id', 'subject_name'], 'unique_class_subject');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
    }
};