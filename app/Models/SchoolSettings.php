<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SchoolSettings extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'school_settings';

    protected $fillable = [
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

    // ── Static helpers ────────────────────────────────────────────────

    /**
     * Get the single settings row.
     *
     * ONLY call this when tenancy is already initialized:
     *   - Inside tenant route controllers
     *   - Inside the receipt blade (standalone, no layout)
     *   - Inside the AppServiceProvider View::composer AFTER the
     *     tenancy()->initialized() guard passes
     *
     * Never call directly from:
     *   - AppServiceProvider without the guard
     *   - Super admin controllers
     *   - Artisan commands without explicit tenant context
     */
    public static function current(): self
    {
        return self::firstOrCreate([], ['school_name' => 'My School']);
    }

    // ── Accessors ─────────────────────────────────────────────────────

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

    public function getFullAddressAttribute(): string
    {
        return collect([
            $this->address_line1,
            $this->address_line2,
            $this->city,
            $this->state,
            $this->pincode,
        ])->filter()->implode(', ');
    }

    public function getFullAddressHiAttribute(): string
    {
        return collect([
            $this->address_line1_hi ?? $this->address_line1,
            $this->address_line2_hi ?? $this->address_line2,
            $this->city_hi          ?? $this->city,
            $this->state_hi         ?? $this->state,
            $this->pincode,
        ])->filter()->implode(', ');
    }
}