<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_applications', function (Blueprint $table) {
            $table->id();

            // Who is applying
            $table->enum('applicant_type', ['student', 'staff']);
            $table->unsignedBigInteger('applicant_id');  // student_profile_id or staff_profile_id

            // The TenantUser who submitted (student/parent/staff user)
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

            // Applied on behalf of (parent applying for student)
            $table->boolean('applied_by_parent')->default(false);

            $table->unsignedBigInteger('leave_type_id');
            $table->foreign('leave_type_id')->references('id')->on('leave_types');

            $table->date('from_date');
            $table->date('to_date');
            $table->unsignedTinyInteger('total_days')->default(1);

            $table->text('reason');
            $table->string('document_path')->nullable();  // optional document upload

            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');

            // Approval tracking
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index(['applicant_type', 'applicant_id']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_applications');
    }
};
