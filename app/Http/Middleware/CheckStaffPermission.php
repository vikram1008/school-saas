<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStaffPermission
{
    /**
     * Check that the authenticated staff member has a specific permission.
     * School admins bypass this check entirely.
     *
     * Usage in routes:
     *   ->middleware('staff.permission:can_mark_student_attendance')
     */
    public function handle(Request $request, Closure $next, string $permission)
    {
        $user = Auth::guard('tenant')->user();

        if (! $user) {
            abort(403, 'Unauthorized.');
        }

        // Admins always pass
        if ($user->isSchoolAdmin()) {
            return $next($request);
        }

        // Staff: check the named permission
        if ($user->isStaff() && $user->hasPermission($permission)) {
            return $next($request);
        }

        abort(403, "You don't have permission to access this module. Contact your school admin.");
    }
}
