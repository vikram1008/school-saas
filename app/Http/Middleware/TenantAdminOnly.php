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

        if (!$user || !$user->isSchoolAdmin()) {
            abort(403, 'Only School Administrators can access this area.');
        }

        return $next($request);
    }
}