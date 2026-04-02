<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();

            $table->string('name');                         // "Nursery", "KG", "Class 6"
            $table->unsignedInteger('order')->default(0);   // for sorting
            $table->boolean('has_sections')->default(true); // two-tier vs single-tier

            // Used only when has_sections = false
            $table->unsignedBigInteger('class_teacher_id')->nullable();

            $table->unsignedInteger('capacity')->nullable(); // max students (single-tier)
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['academic_year_id', 'name']); // no duplicate class names per year
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};