<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParentOnly
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('tenant')->user();

        if (!$user || !$user->isParent()) {
            abort(403, 'This area is for parents only.');
        }

        return $next($request);
    }
}