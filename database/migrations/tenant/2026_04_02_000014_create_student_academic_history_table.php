<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_academic_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')
                  ->unique()
                  ->constrained('student_profiles')
                  ->cascadeOnDelete();

            $table->string('previous_school_name')->nullable();
            $table->enum('previous_school_type', ['government', 'private', 'aided'])
                  ->nullable();
            $table->string('last_class_attended')->nullable();
            $table->enum('last_result', ['pass', 'fail', 'promoted', 'na'])
                  ->nullable();
            $table->string('percentage_grade')->nullable();
            $table->string('tc_number')->nullable();
            $table->date('tc_issue_date')->nullable();
            $table->enum('medium_of_instruction', ['hindi', 'english', 'other'])
                  ->nullable();
            $table->string('medium_other')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_academic_history');
    }
};