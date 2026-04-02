<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use App\Models\Tenant;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SubscriptionController extends Controller
{
    public function __construct(protected SubscriptionService $subscriptionService) {}

    public function index()
    {
        // All latest subscriptions per school
        $subscriptions = Subscription::with('tenant.domains')
            ->whereIn('id', function ($query) {
                $query->selectRaw('MAX(id)')
                      ->from('subscriptions')
                      ->whereNotIn('status', ['paid', 'waived'])
                      ->groupBy('tenant_id');
            })
            ->latest()
            ->get();

        // Summary stats
        $stats = [
            'total'         => Tenant::count(),
            'active'        => $subscriptions->where('status', 'active')->count(),
            'grace_warning' => $subscriptions->where('status', 'grace_warning')->count(),
            'grace_readonly'=> $subscriptions->where('status', 'grace_readonly')->count(),
            'suspended'     => $subscriptions->where('status', 'suspended')->count(),
        ];

        // Overdue revenue at risk
        $overdueRevenue = $subscriptions
            ->whereIn('status', ['grace_warning', 'grace_readonly', 'suspended'])
            ->sum('amount_due');

        return view('superadmin.subscriptions.index', compact(
            'subscriptions', 'stats', 'overdueRevenue'
        ));
    }

    public function show(Tenant $school)
    {
        $school->load('domains');

        // Full history — all cycles
        $history = Subscription::where('tenant_id', $school->id)
            ->latest()
            ->paginate(10);

        // Current active subscription
        $current = $school->activeSubscription()->first();

        return view('superadmin.subscriptions.show', compact(
            'school', 'history', 'current'
        ));
    }

    public function markPaid(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'amount_paid'       => ['required', 'numeric', 'min:0'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ]);

        $this->subscriptionService->markAsPaid(
            $subscription,
            $validated['amount_paid'],
            $validated['payment_reference'] ?? null
        );

        if ($validated['notes']) {
            $subscription->update(['notes' => $validated['notes']]);
        }

        // Create next billing cycle
        $this->subscriptionService->renewSubscription(
            $subscription->tenant,
            $subscription
        );

        return redirect()
            ->route('superadmin.subscriptions.show', $subscription->tenant)
            ->with('success', "Payment of ₹{$validated['amount_paid']} recorded. Next billing cycle created.");
    }

    public function waive(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'notes' => ['required', 'string', 'max:500'],
        ]);

        $this->subscriptionService->waiveSubscription(
            $subscription,
            $validated['notes']
        );

        // Create next billing cycle
        $this->subscriptionService->renewSubscription(
            $subscription->tenant,
            $subscription
        );

        return redirect()
            ->route('superadmin.subscriptions.show', $subscription->tenant)
            ->with('success', "Subscription waived. Next billing cycle created.");
    }

    public function reactivate(Tenant $school)
    {
        $school->update([
            'subscription_status' => 'active',
            'is_active'           => true,
        ]);

        // Update current subscription status
        $subscription = $school->activeSubscription()->first();
        if ($subscription) {
            $subscription->update([
                'status'       => 'active',
                'days_overdue' => 0,
            ]);
        }

        return redirect()
            ->route('superadmin.subscriptions.show', $school)
            ->with('success', "\"{$school->school_name}\" reactivated successfully.");
    }

    public function runCheck()
    {
        Artisan::call('subscriptions:check');
        $output = Artisan::output();

        return redirect()
            ->route('superadmin.subscriptions.index')
            ->with('success', 'Subscription check completed successfully.')
            ->with('check_output', $output);
    }
}