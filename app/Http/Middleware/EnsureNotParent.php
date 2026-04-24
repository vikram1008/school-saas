<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Blocks parents from accessing staff/teacher/admin-facing routes.
 * Allows school_admin, teacher, accountant, librarian, etc.
 */
class EnsureNotParent
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('tenant')->user();

        if (! $user || $user->isParent()) {
            abort(403, 'Parents cannot access this area.');
        }

        return $next($request);
    }
}
