<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_profile_id')
                  ->constrained('staff_profiles')
                  ->cascadeOnDelete();

            $table->enum('document_type', [
                'aadhaar_card',
                'pan_card',
                'degree_certificate',
                'marksheet',
                'experience_certificate',
                'appointment_letter',
                'caste_certificate',
                'disability_certificate',
                'other',
            ]);

            $table->string('file_path')->nullable();
            $table->string('original_name')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_documents');
    }
};