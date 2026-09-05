<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    protected $connection = 'tenant';

    protected $table = 'leave_types';

    protected $fillable = [
        'name',
        'name_hi',
        'max_days_per_year',
        'requires_document',
        'applicable_to_students',
        'applicable_to_staff',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'requires_document' => 'boolean',
        'applicable_to_students' => 'boolean',
        'applicable_to_staff' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaveApplications(): HasMany
    {
        return $this->hasMany(LeaveApplication::class, 'leave_type_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function scopeForStudents($query)
    {
        return $query->where('applicable_to_students', true);
    }

    public function scopeForStaff($query)
    {
        return $query->where('applicable_to_staff', true);
    }
}
