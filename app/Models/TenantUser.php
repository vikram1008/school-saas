<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TenantUser extends Authenticatable
{
    use Notifiable;

    // Points to tenant DB connection
    protected $connection = 'tenant';

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function staffPermission(): HasOne
    {
        return $this->hasOne(StaffPermission::class, 'user_id');
    }

    public function leaveApplications(): HasMany
    {
        return $this->hasMany(LeaveApplication::class, 'user_id');
    }

    // ── Role helpers ───────────────────────────────────────────────

    public function isSchoolAdmin(): bool
    {
        return $this->role === 'school_admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    public function isAccountant(): bool
    {
        return $this->role === 'accountant';
    }

    public function isLibrarian(): bool
    {
        return $this->role === 'librarian';
    }

    /**
     * Returns true for any non-admin, non-student, non-parent staff member.
     */
    public function isStaff(): bool
    {
        return in_array($this->role, ['teacher', 'accountant', 'librarian'], true);
    }

    public function getDashboardRoute(): string
    {
        return match ($this->role) {
            'school_admin' => 'tenant.dashboard',
            'teacher' => 'tenant.staff.dashboard',
            'accountant' => 'tenant.staff.dashboard',
            'librarian' => 'tenant.staff.dashboard',
            'student' => 'tenant.student.dashboard',
            'parent' => 'tenant.parent-portal.dashboard',
            default => 'tenant.dashboard',
        };
    }

    /**
     * Resolve the staff permission record, creating defaults if missing.
     * Uses the DB directly to avoid Eloquent's relation cache returning a stale null.
     */
    public function resolvedPermissions(): StaffPermission
    {
        // If already loaded and not null, return from cache
        if ($this->relationLoaded('staffPermission') && $this->staffPermission !== null) {
            return $this->staffPermission;
        }

        // Load fresh from DB (or create with role defaults if missing)
        $permission = StaffPermission::firstOrCreate(
            ['user_id' => $this->id],
            StaffPermission::defaultsForRole($this->role)
        );

        // Cache it in the relation so further calls in the same request are cheap
        $this->setRelation('staffPermission', $permission);

        return $permission;
    }

    /**
     * Check a named staff permission key (e.g. 'can_enter_marks').
     * School admins always return true.
     */
    public function hasPermission(string $ability): bool
    {
        if ($this->isSchoolAdmin()) {
            return true;
        }

        if ($this->isStaff()) {
            $perms = $this->resolvedPermissions();

            return (bool) ($perms->$ability ?? false);
        }

        return false;
    }

    /**
     * Abort with 403 if the user does not have the given permission.
     * School admins are always allowed through.
     */
    public function authorizePermission(string $ability): void
    {
        if (! $this->hasPermission($ability)) {
            abort(403, "You don't have permission to perform this action. Contact your school admin.");
        }
    }
}
