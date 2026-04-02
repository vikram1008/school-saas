<?php

namespace App\Http\Middleware;

use App\Models\SaasSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSubscriptionStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        if (!$tenant) {
            return $next($request);
        }

        $status = $tenant->subscription_status;

        // Phase 3 — Fully suspended
        if ($status === 'suspended') {
            if ($request->expectsJson()) {
                return response()->json([
                    'error'   => 'Account suspended.',
                    'message' => 'Please contact your administrator to reactivate.',
                ], 403);
            }

            return response()->view('tenant.suspended', [
                'school'        => $tenant,
                'support_email' => SaasSettings::get('support_email', 'support@saas.com'),
                'saas_name'     => SaasSettings::get('saas_name', 'School SaaS'),
            ], 403);
        }

        // Phase 2 — Read-Only: block all write operations
        if ($status === 'grace_readonly') {
            if (in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error'   => 'Account is in Read-Only mode.',
                        'message' => 'Payment is overdue. Please contact your administrator.',
                    ], 403);
                }

                return back()->withErrors([
                    'subscription' => 'Your account is in Read-Only mode due to an overdue payment. Contact your administrator.',
                ]);
            }
        }

        // Phase 1 & 2 — Share banner data with all tenant views
        if (in_array($status, ['grace_warning', 'grace_readonly'])) {
            $subscription = $tenant->activeSubscription()->first();

            view()->share('subscriptionBanner', [
                'status'        => $status,
                'days_overdue'  => $subscription?->days_overdue ?? 0,
                'amount_due'    => $subscription?->amount_due ?? 0,
                'support_email' => SaasSettings::get('support_email', 'support@saas.com'),
            ]);
        }

        return $next($request);
    }
}