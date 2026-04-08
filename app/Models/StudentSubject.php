<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSubject extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'student_subjects';

    protected $fillable = [
        'student_profile_id',
        'stream',
        'subject_1', 'subject_1_hi',
        'subject_2', 'subject_2_hi',
        'subject_3', 'subject_3_hi',
        'subject_4', 'subject_4_hi',
        'subject_5', 'subject_5_hi',
        'additional_subject', 'additional_subject_hi',
    ];

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }
}