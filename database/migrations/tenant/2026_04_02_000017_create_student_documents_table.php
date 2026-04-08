<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')
                  ->constrained('student_profiles')
                  ->cascadeOnDelete();

            $table->enum('document_type', [
                'birth_certificate',
                'transfer_certificate',
                'marksheet',
                'aadhaar_card',
                'jan_aadhaar_card',
                'caste_certificate',
                'income_certificate',
                'bpl_card',
                'disability_certificate',
                'other',
            ]);

            $table->string('file_path')->nullable();    // stored path
            $table->string('original_name')->nullable(); // original filename
            $table->boolean('is_verified')->default(false);
            $table->string('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};