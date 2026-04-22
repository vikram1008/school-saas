<?php

namespace App\Models;

/**
 * SchoolSettings — Proxy / Compatibility Shim
 *
 * Previously this was an Eloquent model pointing to a per-tenant
 * `school_settings` table. After the consolidation refactor all school
 * identity & branding data lives on the central `Tenant` model.
 *
 * This class now acts as a lightweight proxy so that existing view code that
 * calls `SchoolSettings::current()` or uses `$schoolSettings->foo` continues
 * to work without any changes — it simply returns the current Tenant instance.
 *
 * All field accessors (logo_url, favicon_url, full_address, …) are defined
 * directly on the Tenant model.
 */
class SchoolSettings
{
    /**
     * Return the current school's Tenant model (single source of truth).
     *
     * Safe to call only when tenancy is initialised:
     *   - Inside tenant route controllers
     *   - Inside the AppServiceProvider View::composer AFTER the
     *     tenancy()->initialized() guard passes
     *   - Inside Blade views that are served on a tenant domain
     *
     * Never call from:
     *   - Super admin controllers
     *   - Artisan commands without explicit tenant context
     */
    public static function current(): ?Tenant
    {
        return tenant();
    }
}
