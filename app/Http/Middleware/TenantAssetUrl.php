<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TenantAssetUrl
{
    public function handle(Request $request, Closure $next)
    {
        if (tenant()) {
            // Override asset URL to use current tenant's domain
            // so all assets load same-origin — no CORS needed
            $tenantUrl = $request->getScheme() . '://' . $request->getHost();
            app('config')->set('app.asset_url', $tenantUrl);
        }

        return $next($request);
    }
}