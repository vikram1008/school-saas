<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check.subscription'  => \App\Http\Middleware\CheckSubscriptionStatus::class,
            'tenant.assets'      => \App\Http\Middleware\TenantAssetUrl::class,
            'tenant.admin' => \App\Http\Middleware\TenantAdminOnly::class,
            'parent.only' => \App\Http\Middleware\ParentOnly::class,
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
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, Request $request) {
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
