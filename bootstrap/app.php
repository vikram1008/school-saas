<?php

use App\Http\Middleware\CheckStaffPermission;
use App\Http\Middleware\CheckSubscriptionStatus;
use App\Http\Middleware\EnsureNotParent;
use App\Http\Middleware\ParentOnly;
use App\Http\Middleware\TenantAdminOnly;
use App\Http\Middleware\TenantAssetUrl;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'check.subscription' => CheckSubscriptionStatus::class,
            'tenant.assets' => TenantAssetUrl::class,
            'tenant.admin' => TenantAdminOnly::class,
            'parent.only' => ParentOnly::class,
            'not.parent' => EnsureNotParent::class,
            'staff.permission' => CheckStaffPermission::class,
        ]);

        // Smart Unauthenticated Redirect Logic
        $middleware->redirectGuestsTo(function (Request $request) {

            if ($request->is('superadmin') || $request->is('superadmin/*')) {
                return route('superadmin.login');
            }

            return url('/login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Tenant subdomain → redirect to tenant login
            if (tenant()) {
                return redirect()->route('tenant.login');
            }

            // Central → redirect to super admin login
            return redirect()->route('superadmin.login');
        });
    })
    ->create();
