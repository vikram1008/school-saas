<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendancePeriod extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'attendance_periods';

    protected $fillable = [
        'class_id', 'section_id',
        'period_number', 'subject_name', 'subject_name_hi',
        'teacher_id', 'day_of_week',
        'start_time', 'end_time', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function teacher()
    {
        return $this->belongsTo(TenantUser::class, 'teacher_id');
    }
}