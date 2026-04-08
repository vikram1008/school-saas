<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_bank_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')
                  ->unique()
                  ->constrained('student_profiles')
                  ->cascadeOnDelete();

            $table->string('bank_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('account_number')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->enum('account_holder', ['student', 'parent'])
                  ->default('parent');
            $table->string('account_holder_name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_bank_details');
    }
};