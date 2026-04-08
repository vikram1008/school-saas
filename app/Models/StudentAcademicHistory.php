<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAcademicHistory extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'student_academic_history';

    protected $fillable = [
        'student_profile_id',
        'previous_school_name',
        'previous_school_type',
        'last_class_attended',
        'last_result',
        'percentage_grade',
        'tc_number',
        'tc_issue_date',
        'medium_of_instruction',
        'medium_other',
    ];

    protected $casts = [
        'tc_issue_date' => 'date',
    ];

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }
}