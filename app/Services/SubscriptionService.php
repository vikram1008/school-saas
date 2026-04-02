<?php

namespace App\Services;

use App\Models\SaasSettings;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Create the first subscription when a school is provisioned.
     */
    public function createInitialSubscription(Tenant $tenant): Subscription
    {
        $cycle      = $tenant->billing_cycle;
        $anchorDate = $tenant->provisioned_at ?? now()->toDate();
        $periodEnd  = $this->calculatePeriodEnd($anchorDate, $cycle);

        // Snapshot student count — 0 at provisioning time
        $studentCount = 0;
        $amountDue    = $studentCount * $tenant->per_student_rate;

        return Subscription::create([
            'tenant_id'              => $tenant->id,
            'billing_cycle'          => $cycle,
            'period_start'           => $anchorDate,
            'period_end'             => $periodEnd,
            'due_date'               => $periodEnd,
            'student_count_snapshot' => $studentCount,
            'per_student_rate'       => $tenant->per_student_rate,
            'amount_due'             => $amountDue,
            'status'                 => 'active',
            'days_overdue'           => 0,
        ]);
    }

    /**
     * Create the next subscription cycle after current one ends.
     */
    public function renewSubscription(Tenant $tenant, Subscription $current): Subscription
    {
        $newStart = $current->period_end->addDay();
        $newEnd   = $this->calculatePeriodEnd($newStart, $current->billing_cycle);

        // Snapshot live student count from tenant DB
        $studentCount = $this->getStudentCount($tenant);
        $amountDue    = $studentCount * $tenant->per_student_rate;

        return Subscription::create([
            'tenant_id'              => $tenant->id,
            'billing_cycle'          => $current->billing_cycle,
            'period_start'           => $newStart,
            'period_end'             => $newEnd,
            'due_date'               => $newEnd,
            'student_count_snapshot' => $studentCount,
            'per_student_rate'       => $tenant->per_student_rate,
            'amount_due'             => $amountDue,
            'status'                 => 'active',
            'days_overdue'           => 0,
        ]);
    }

    /**
     * Calculate period end date based on billing cycle.
     */
    public function calculatePeriodEnd(\DateTimeInterface|\Carbon\Carbon $startDate, string $cycle): \Carbon\Carbon
    {
        $start = \Carbon\Carbon::parse($startDate);

        return match($cycle) {
            'monthly'     => $start->copy()->addMonth()->subDay(),
            'quarterly'   => $start->copy()->addMonths(3)->subDay(),
            'half_yearly' => $start->copy()->addMonths(6)->subDay(),
            'yearly'      => $start->copy()->addYear()->subDay(),
            default       => $start->copy()->addMonth()->subDay(),
        };
    }

    /**
     * Get live active student count from tenant DB.
     */
    public function getStudentCount(Tenant $tenant): int
    {
        try {
            tenancy()->initialize($tenant);
            $count = DB::connection('tenant')
                ->table('users')
                ->where('role', 'student')
                ->where('is_active', true)
                ->count();
            tenancy()->end();
            return $count;
        } catch (\Exception $e) {
            tenancy()->end();
            return 0;
        }
    }

    /**
     * Mark a subscription as paid.
     */
    public function markAsPaid(
        Subscription $subscription,
        float $amountPaid,
        string $reference = null
    ): void {
        $subscription->update([
            'status'            => 'paid',
            'amount_paid'       => $amountPaid,
            'paid_at'           => now(),
            'payment_reference' => $reference,
            'days_overdue'      => 0,
        ]);

        // Reset tenant subscription status to active
        $subscription->tenant->update([
            'subscription_status' => 'active',
        ]);
    }

    /**
     * Waive a subscription (Super Admin override).
     */
    public function waiveSubscription(Subscription $subscription, string $notes = null): void
    {
        $subscription->update([
            'status'      => 'waived',
            'notes'       => $notes,
            'days_overdue'=> 0,
        ]);

        $subscription->tenant->update([
            'subscription_status' => 'active',
        ]);
    }
}