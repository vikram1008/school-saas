<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_demands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')
                  ->constrained('student_profiles')
                  ->cascadeOnDelete();
            $table->foreignId('fee_head_id')
                  ->constrained('fee_heads')
                  ->cascadeOnDelete();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();

            // Period info
            $table->string('period_label');      // "April 2025", "Q1 2025-26", "2025-26"
            $table->date('period_start');
            $table->date('period_end');
            $table->date('due_date');

            // Amount
            $table->decimal('amount_due', 10, 2);
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->decimal('fine_amount', 10, 2)->default(0);

            // Status
            $table->enum('status', [
                'pending',
                'partial',
                'paid',
                'waived',
                'overdue',
            ])->default('pending');

            $table->string('waive_reason')->nullable();
            $table->timestamps();

            $table->unique(
                ['student_profile_id', 'fee_head_id', 'period_label'],
                'unique_fee_demand'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_demands');
    }
};