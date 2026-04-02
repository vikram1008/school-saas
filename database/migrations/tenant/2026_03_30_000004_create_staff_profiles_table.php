<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Personal Info
            $table->string('employee_code')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('blood_group')->nullable();
            $table->string('photo')->nullable();
            $table->string('phone')->nullable();

            // Professional Info
            $table->string('designation')->nullable();   // e.g. "Math Teacher"
            $table->string('department')->nullable();    // e.g. "Science"
            $table->date('joining_date')->nullable();
            $table->enum('employment_type', [
                'full_time',
                'part_time',
                'contract',
                'substitute',
            ])->default('full_time');
            $table->enum('status', [
                'active',
                'inactive',
                'on_leave',
                'resigned',
                'terminated',
            ])->default('active');

            // Salary
            $table->decimal('salary', 10, 2)->nullable();

            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();

            // Documents
            $table->string('id_proof_type')->nullable();   // Aadhaar, PAN, etc.
            $table->string('id_proof_number')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_profiles');
    }
};