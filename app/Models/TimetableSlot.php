<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableSlot extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'timetable_slots';

    protected $fillable = [
        'academic_year_id', 'class_id', 'section_id',
        'period_number', 'period_name',
        'start_time', 'end_time',
        'day_of_week', 'is_break', 'is_active',
    ];

    protected $casts = [
        'is_break'  => 'boolean',
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

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public static function dayLabels(): array
    {
        return [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
        ];
    }

    public static function dayShort(): array
    {
        return [
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
        ];
    }
}