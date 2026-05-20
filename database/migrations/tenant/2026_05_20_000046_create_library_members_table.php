<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_members', function (Blueprint $table) {
            $table->id();
            $table->string('member_type'); // student, staff
            $table->unsignedBigInteger('user_id')->nullable(); // tenant user id
            $table->unsignedBigInteger('profile_id')->nullable(); // student_profile or staff_profile id
            $table->string('member_number')->unique(); // e.g. LIB-2024-001
            $table->date('membership_start')->nullable();
            $table->date('membership_expiry')->nullable();
            $table->integer('max_books_allowed')->default(3); // max simultaneous issues
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_members');
    }
};
