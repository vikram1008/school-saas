<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TenantUser extends Authenticatable
{
    use Notifiable;

    // Points to tenant DB connection
    protected $connection = 'tenant';
    protected $table      = 'users';

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
        'is_active'         => 'boolean',
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // Role helpers
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

    public function getDashboardRoute(): string
    {
        return match($this->role) {
            'school_admin' => 'tenant.dashboard',
            'teacher'      => 'tenant.dashboard',
            'accountant'   => 'tenant.dashboard',
            'student'      => 'tenant.student.dashboard',
            'parent'       => 'tenant.parent-portal.dashboard',
            default        => 'tenant.dashboard',
        };
    }
}