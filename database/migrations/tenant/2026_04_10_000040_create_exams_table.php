<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->string('name');
            $table->string('name_hi')->nullable();
            $table->enum('exam_type', [
                'unit_test',
                'half_yearly',
                'annual',
                'quarterly',
                'pre_board',
                'other',
            ])->default('unit_test');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_published')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};