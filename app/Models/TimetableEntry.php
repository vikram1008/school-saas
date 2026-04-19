<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimetableEntry extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'timetable_entries';

    protected $fillable = [
        'academic_year_id', 'class_id', 'section_id',
        'day_of_week', 'period_number',
        'subject_name', 'subject_name_hi',
        'teacher_id', 'room_number',
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
        return $this->belongsTo(StaffProfile::class, 'teacher_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}