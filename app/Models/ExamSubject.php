<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamSubject extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'exam_subjects';

    protected $fillable = [
        'exam_id', 'class_id', 'section_id',
        'subject_name', 'subject_name_hi',
        'max_marks', 'pass_marks', 'sort_order',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function marks()
    {
        return $this->hasMany(StudentMark::class);
    }
}