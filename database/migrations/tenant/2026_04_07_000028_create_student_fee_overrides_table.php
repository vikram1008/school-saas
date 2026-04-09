<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fee_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')
                  ->constrained('student_profiles')
                  ->cascadeOnDelete();
            $table->foreignId('fee_head_id')
                  ->constrained('fee_heads')
                  ->cascadeOnDelete();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('reason')->nullable();
            $table->timestamps();

            $table->unique(
                ['student_profile_id', 'fee_head_id', 'academic_year_id'],
                'unique_student_override'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fee_overrides');
    }
};