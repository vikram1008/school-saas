<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TenantAdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('tenant')->user();

        if (! $user) {
            abort(403, 'Unauthorized.');
        }

        // School admins have full access
        if ($user->isSchoolAdmin()) {
            return $next($request);
        }

        // Staff members (teacher, accountant, librarian) are allowed through;
        // per-route permission checks are enforced separately.
        if ($user->isStaff()) {
            return $next($request);
        }

        abort(403, 'Only School Administrators and authorised staff can access this area.');
    }
}
