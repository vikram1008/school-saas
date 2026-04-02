<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Personal Info
            $table->string('admission_number')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->string('blood_group')->nullable();
            $table->string('photo')->nullable();
            $table->string('phone')->nullable();

            // Academic Info
            $table->string('class')->nullable();       // e.g. "10-A"
            $table->string('section')->nullable();
            $table->year('admission_year')->nullable();
            $table->enum('status', [
                'active',
                'inactive',
                'graduated',
                'transferred',
                'dropped',
            ])->default('active');

            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();

            // Guardian linkage (points to parent_profiles)
            $table->unsignedBigInteger('parent_profile_id')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_profiles');
    }
};