<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_profile_id')
                  ->constrained('staff_profiles')
                  ->cascadeOnDelete();

            $table->date('date');
            $table->enum('status', [
                'present',
                'absent',
                'late',
                'half_day',
                'leave',
                'holiday',
            ])->default('present');

            $table->string('in_time')->nullable();   // "09:00"
            $table->string('out_time')->nullable();  // "17:00"
            $table->unsignedBigInteger('marked_by');
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->unique(['staff_profile_id', 'date'], 'unique_staff_attendance');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_attendance');
    }
};