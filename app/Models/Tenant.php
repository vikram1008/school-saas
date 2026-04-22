<?php

namespace App\Models;

use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * The central Tenant model is the single source of truth for every school's
 * identity, branding, and billing information.
 *
 * Fields are split into two categories:
 *   - SCHOOL IDENTITY  — editable by the school admin via SchoolSettingsController
 *   - BILLING / STATUS — editable by super admin only via SchoolController
 */
class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    // ── School Identity fields (school admin can edit) ─────────────────────
    public const SCHOOL_EDITABLE_FIELDS = [
        'school_name', 'school_name_hi',
        'logo', 'favicon',
        'email', 'phone', 'phone_alt', 'website',
        'address_line1', 'address_line1_hi',
        'address_line2', 'address_line2_hi',
        'city', 'city_hi',
        'state', 'state_hi',
        'pincode', 'country',
        'board_affiliation', 'school_code',
        'affiliation_number', 'udise_code',
        'primary_color', 'tagline', 'tagline_hi',
        'receipt_footer_note', 'receipt_footer_note_hi',
        'principal_name', 'principal_name_hi',
        'principal_signature',
    ];

    // ── Billing / Status fields (super admin only) ─────────────────────────
    public const BILLING_ONLY_FIELDS = [
        'per_student_rate', 'billing_cycle',
        'subscription_status', 'provisioned_at', 'is_active',
    ];

    protected $fillable = [
        'id',
        // Identity
        'school_name', 'school_name_hi',
        'logo', 'favicon',
        'email', 'phone', 'phone_alt', 'website',
        'address',        // legacy single-line field (superadmin basic view)
        'address_hi',     // legacy
        'address_line1', 'address_line1_hi',
        'address_line2', 'address_line2_hi',
        'city', 'city_hi',
        'state', 'state_hi',
        'pincode', 'country',
        'board_affiliation', 'school_code',
        'affiliation_number', 'udise_code',
        'primary_color', 'tagline', 'tagline_hi',
        'receipt_footer_note', 'receipt_footer_note_hi',
        'principal_name', 'principal_name_hi',
        'principal_signature',
        // Billing / Status
        'per_student_rate', 'billing_cycle',
        'subscription_status', 'provisioned_at',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'per_student_rate' => 'integer',
        'provisioned_at' => 'date',
        'data' => 'array',
    ];

    /**
     * Declare these as real DB columns (not JSON data fields) for Stancl Tenancy.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            // Identity
            'school_name', 'school_name_hi',
            'logo', 'favicon',
            'email', 'phone', 'phone_alt', 'website',
            'address', 'address_hi',
            'address_line1', 'address_line1_hi',
            'address_line2', 'address_line2_hi',
            'city', 'city_hi',
            'state', 'state_hi',
            'pincode', 'country',
            'board_affiliation', 'school_code',
            'affiliation_number', 'udise_code',
            'primary_color', 'tagline', 'tagline_hi',
            'receipt_footer_note', 'receipt_footer_note_hi',
            'principal_name', 'principal_name_hi',
            'principal_signature',
            // Billing
            'per_student_rate', 'billing_cycle',
            'subscription_status', 'provisioned_at',
            'is_active',
            // SuperAdmin provisioning helpers (written once, never stored)
            'admin_name', 'admin_email', 'admin_password',
        ];
    }

    // ── Accessors (previously lived on SchoolSettings) ─────────────────────

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return Storage::url($this->logo);
        }

        return asset('assets/img/school-logo-placeholder.png');
    }

    public function getFaviconUrlAttribute(): string
    {
        if ($this->favicon && Storage::disk('public')->exists($this->favicon)) {
            return Storage::url($this->favicon);
        }

        return asset('assets/img/favicon.png');
    }

    public function getPrincipalSignatureUrlAttribute(): ?string
    {
        if ($this->principal_signature && Storage::disk('public')->exists($this->principal_signature)) {
            return Storage::url($this->principal_signature);
        }

        return null;
    }

    /**
     * Full structured address (uses address_line1/2 if set, falls back to legacy `address`).
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line1 ?: $this->address,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->pincode,
        ]);

        return implode(', ', $parts);
    }

    public function getFullAddressHiAttribute(): string
    {
        $parts = array_filter([
            $this->address_line1_hi ?? ($this->address_line1 ?: $this->address_hi ?? $this->address),
            $this->address_line2_hi ?? $this->address_line2,
            $this->city_hi ?? $this->city,
            $this->state_hi ?? $this->state,
            $this->pincode,
        ]);

        return implode(', ', $parts);
    }

    // ── Billing helpers ────────────────────────────────────────────────────

    /** Calculate monthly bill for this school with a given student count. */
    public function calculateMonthlyBill(int $activeStudentCount): int
    {
        return $activeStudentCount * $this->per_student_rate;
    }

    public function getPrimaryDomainAttribute(): ?string
    {
        return $this->domains->first()?->domain;
    }

    // ── Status helpers ─────────────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->subscription_status === 'active';
    }

    public function isReadOnly(): bool
    {
        return $this->subscription_status === 'grace_readonly';
    }

    public function isSuspended(): bool
    {
        return $this->subscription_status === 'suspended';
    }

    public function isInGrace(): bool
    {
        return in_array($this->subscription_status, ['grace_warning', 'grace_readonly']);
    }

    // ── Relationships ──────────────────────────────────────────────────────

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'tenant_id');
    }

    /**
     * The most recent subscription not yet paid/waived.
     * Used by CheckSubscriptionStatus middleware and billing banners.
     */
    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class, 'tenant_id')
            ->whereNotIn('status', ['paid', 'waived'])
            ->latest();
    }

    /**
     * The most recent subscription regardless of status.
     * Use this on the dashboard to display the current billing cycle.
     */
    public function latestSubscription()
    {
        return $this->hasOne(Subscription::class, 'tenant_id')
            ->latest();
    }
}
