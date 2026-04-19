<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_student_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_profile_id')
                  ->constrained('parent_profiles')
                  ->cascadeOnDelete();
            $table->foreignId('student_profile_id')
                  ->constrained('student_profiles')
                  ->cascadeOnDelete();
            $table->enum('relationship', [
                'father', 'mother', 'guardian', 'other'
            ])->default('father');
            $table->boolean('is_primary')->default(true);
            $table->timestamps();

            $table->unique(
                ['parent_profile_id', 'student_profile_id'],
                'unique_parent_student'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_student_links');
    }
};