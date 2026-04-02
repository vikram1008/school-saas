<?php

namespace App\Console\Commands;

use App\Models\SaasSettings;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckSubscriptions extends Command
{
    protected $signature   = 'subscriptions:check';
    protected $description = 'Daily check — updates subscription statuses and tenant access levels.';

    public function handle(SubscriptionService $subscriptionService): void
    {
        // Load thresholds from settings
        $warningDays  = SaasSettings::get('grace_warning_days',  7);
        $readonlyDays = SaasSettings::get('grace_readonly_days', 30);
        $suspendDays  = SaasSettings::get('suspension_days',     31);

        $this->info("Running subscription check — " . now()->toDateString());
        $this->info("Thresholds → Warning: {$warningDays}d | Read-Only: {$readonlyDays}d | Suspend: {$suspendDays}d");
        $this->newLine();

        $tenants = Tenant::with('domains')->get();

        $counts = [
            'active'         => 0,
            'grace_warning'  => 0,
            'grace_readonly' => 0,
            'suspended'      => 0,
            'renewed'        => 0,
            'skipped'        => 0,
        ];

        foreach ($tenants as $tenant) {
            $this->line("Processing: {$tenant->school_name} ({$tenant->id})");

            // Get current active subscription (not paid/waived)
            $subscription = Subscription::where('tenant_id', $tenant->id)
                ->whereNotIn('status', ['paid', 'waived'])
                ->latest()
                ->first();

            if (!$subscription) {
                $this->warn("  → No active subscription found. Skipping.");
                $counts['skipped']++;
                continue;
            }

            $today      = now()->startOfDay();
            $periodEnd  = $subscription->period_end->startOfDay();
            $daysOverdue = $today->diffInDays($periodEnd, false) * -1;
            // negative = future (not overdue), positive = overdue

            // Not overdue yet — check if cycle ended and needs renewal
            if ($daysOverdue <= 0) {
                $daysRemaining = $today->diffInDays($periodEnd, false);
                $this->info("  → Active. {$daysRemaining} days remaining.");

                // Snapshot student count mid-cycle for accuracy
                $studentCount = $subscriptionService->getStudentCount($tenant);
                $amountDue    = $studentCount * $tenant->per_student_rate;

                $subscription->update([
                    'student_count_snapshot' => $studentCount,
                    'amount_due'             => $amountDue,
                    'days_overdue'           => 0,
                    'status'                 => 'active',
                ]);

                $tenant->update(['subscription_status' => 'active']);
                $counts['active']++;
                continue;
            }

            // Overdue — determine phase
            $newStatus = match(true) {
                $daysOverdue >= $suspendDays  => 'suspended',
                $daysOverdue >= $warningDays  => 'grace_readonly',
                $daysOverdue >= 1             => 'grace_warning',
                default                       => 'active',
            };

            $this->warn("  → Overdue {$daysOverdue} days → Status: {$newStatus}");

            $subscription->update([
                'status'       => $newStatus,
                'days_overdue' => $daysOverdue,
            ]);

            $tenant->update([
                'subscription_status' => $newStatus,
                'is_active'           => $newStatus !== 'suspended',
            ]);

            $counts[$newStatus]++;
        }

        $this->newLine();
        $this->table(
            ['Status', 'Count'],
            collect($counts)->map(fn($v, $k) => [ucfirst(str_replace('_', ' ', $k)), $v])->values()->toArray()
        );

        $this->info('Subscription check complete.');
    }
}