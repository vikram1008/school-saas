<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->cascadeOnDelete();

            // Billing cycle
            $table->enum('billing_cycle', [
                'monthly', 'quarterly', 'half_yearly', 'yearly'
            ])->default('monthly');

            // Period
            $table->date('period_start');
            $table->date('period_end');
            $table->date('due_date');

            // Snapshotted values at cycle start — never changes after snapshot
            $table->unsignedInteger('student_count_snapshot')->default(0);
            $table->unsignedInteger('per_student_rate');
            $table->decimal('amount_due', 10, 2);

            // Status
            $table->enum('status', [
                'active',
                'grace_warning',
                'grace_readonly',
                'suspended',
                'paid',
                'waived',
            ])->default('active');

            // Payment tracking
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->unsignedInteger('days_overdue')->default(0);

            // Super Admin notes
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};