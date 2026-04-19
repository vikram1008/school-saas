<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMark extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'student_marks';

    protected $fillable = [
        'exam_id', 'student_profile_id',
        'exam_subject_id', 'marks_obtained',
        'is_absent', 'remarks',
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'is_absent'      => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }

    public function examSubject()
    {
        return $this->belongsTo(ExamSubject::class);
    }
}