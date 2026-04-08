<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_family_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')
                  ->unique()
                  ->constrained('student_profiles')
                  ->cascadeOnDelete();

            // Father
            $table->string('father_name')->nullable();
            $table->string('father_occupation')->nullable();
            $table->string('father_annual_income')->nullable();
            $table->string('father_mobile')->nullable();
            $table->string('father_aadhaar')->nullable();

            // Mother
            $table->string('mother_name')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->string('mother_annual_income')->nullable();
            $table->string('mother_mobile')->nullable();
            $table->string('mother_aadhaar')->nullable();

            // Guardian (if not living with parents)
            $table->string('guardian_name')->nullable();
            $table->string('guardian_relationship')->nullable();
            $table->string('guardian_mobile')->nullable();
            $table->string('guardian_occupation')->nullable();
            $table->string('guardian_annual_income')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_family_details');
    }
};