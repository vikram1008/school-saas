<?php

namespace App\Providers;

use App\Models\SchoolSettings;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ── Vite style tag attributes (Vuexy requirement) ─────────────
        Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
            if ($src !== null) {
                return [
                    'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src)
                        ? 'template-customizer-core-css'
                        : (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src)
                            ? 'template-customizer-theme-css'
                            : '')
                ];
            }
            return [];
        });

        // ── Share $schoolSettings with all tenant views ───────────────
        //
        // Why both guards are needed:
        //   View::composer fires for every view whose name matches the
        //   pattern — including when super admin pages @include a
        //   tenant.* partial. The guards ensure we never touch the
        //   tenant DB unless a tenant is actually active.
        //
        // $schoolSettings will be null on super admin / central pages.
        // All tenant views must use null-safe: $schoolSettings?->school_name
        //
        View::composer(
            ['layouts.tenant', 'tenant.*'],
            function ($view) {
                // Guard 1: tenancy package must be bound in the container
                if (! app()->bound('tenancy')) {
                    $view->with('schoolSettings', null);
                    return;
                }

                // Guard 2: a tenant must be currently active
                if (! tenancy()->initialized()) {
                    $view->with('schoolSettings', null);
                    return;
                }

                // Safe to query the tenant DB
                try {
                    $view->with('schoolSettings', SchoolSettings::current());
                } catch (\Throwable $e) {
                    // Tenant DB exists but school_settings table not yet
                    // migrated (e.g. during fresh provisioning). Fail silently.
                    $view->with('schoolSettings', null);
                }
            }
        );
    }
}