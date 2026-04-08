<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $fillable = [
        'id',
        'school_name',
        'school_name_hi',
        'email',
        'phone',
        'address',
        'address_hi',
        'logo',
        'per_student_rate',
        'billing_cycle',
        'subscription_status',
        'provisioned_at', 
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'per_student_rate'=> 'integer',
        'provisioned_at'  => 'date',
        'data'      => 'array',
    ];

    // ← This is the critical fix
    // Tells tenancy these are real DB columns, not JSON data fields
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'school_name',
            'school_name_hi',
            'email',
            'phone',
            'address',
            'address_hi',
            'logo',
            'per_student_rate',
            'billing_cycle',
            'subscription_status',
            'provisioned_at', 
            'is_active',
            'admin_name',
            'admin_email',
            'admin_password',
        ];
    }

    /**
     * Calculate monthly bill for this school.
     * Called with live student count from tenant DB.
     */
    public function calculateMonthlyBill(int $activeStudentCount): int
    {
        return $activeStudentCount * $this->per_student_rate;
    }

    public function getPrimaryDomainAttribute(): ?string
    {
        return $this->domains->first()?->domain;
    }

    // Convenience helpers
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

    // Relationship
    public function subscriptions()
    {
        return $this->hasMany(\App\Models\Subscription::class, 'tenant_id');
    }

    public function activeSubscription()
    {
        return $this->hasOne(\App\Models\Subscription::class, 'tenant_id')
            ->whereNotIn('status', ['paid', 'waived'])
            ->latest();
    }

}