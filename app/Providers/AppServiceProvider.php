<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ── Vite style tag attributes (Vuexy requirement) ─────────────────
        Vite::useStyleTagAttributes(function (?string $src, string $url, ?array $chunk, ?array $manifest) {
            if ($src !== null) {
                return [
                    'class' => preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?core)-?.*/i", $src)
                        ? 'template-customizer-core-css'
                        : (preg_match("/(resources\/assets\/vendor\/scss\/(rtl\/)?theme)-?.*/i", $src)
                            ? 'template-customizer-theme-css'
                            : ''),
                ];
            }

            return [];
        });

        // ── Share $schoolSettings with all tenant views ───────────────────
        //
        // After consolidation, $schoolSettings IS the Tenant model — it now
        // carries all school identity/branding fields directly.
        //
        // Views can use: $schoolSettings->school_name, ->logo_url, ->favicon_url,
        //                ->full_address, ->primary_color, ->principal_name, …
        //
        // On super-admin / central pages $schoolSettings is null.
        // Use null-safe: $schoolSettings?->school_name
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

                // tenant() returns the Tenant Eloquent model — single source of truth.
                $view->with('schoolSettings', tenant());
            }
        );
    }
}
