<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'student_attendance';

    protected $fillable = [
        'student_profile_id', 'class_id', 'section_id',
        'academic_year_id', 'date', 'status',
        'attendance_type', 'period_id', 'subject_name',
        'marked_by', 'remarks',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function period()
    {
        return $this->belongsTo(AttendancePeriod::class, 'period_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(TenantUser::class, 'marked_by');
    }

    public static function statusLabels(): array
    {
        return [
            'present'  => 'Present / उपस्थित',
            'absent'   => 'Absent / अनुपस्थित',
            'late'     => 'Late / देर से',
            'half_day' => 'Half Day / अर्ध दिवस',
            'leave'    => 'Leave / अवकाश',
        ];
    }

    public static function statusColors(): array
    {
        return [
            'present'  => 'success',
            'absent'   => 'danger',
            'late'     => 'warning',
            'half_day' => 'info',
            'leave'    => 'secondary',
        ];
    }
}