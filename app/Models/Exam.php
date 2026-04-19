<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table      = 'exams';

    protected $fillable = [
        'academic_year_id', 'name', 'name_hi',
        'exam_type', 'start_date', 'end_date',
        'is_published', 'description',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'is_published' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function subjects()
    {
        return $this->hasMany(ExamSubject::class);
    }

    public function marks()
    {
        return $this->hasMany(StudentMark::class);
    }

    public static function typeLabels(): array
    {
        return [
            'unit_test'   => 'Unit Test / इकाई परीक्षा',
            'half_yearly' => 'Half Yearly / अर्द्धवार्षिक',
            'annual'      => 'Annual / वार्षिक',
            'quarterly'   => 'Quarterly / त्रैमासिक',
            'pre_board'   => 'Pre Board / प्री बोर्ड',
            'other'       => 'Other / अन्य',
        ];
    }

    public static function typeColors(): array
    {
        return [
            'unit_test'   => 'info',
            'half_yearly' => 'warning',
            'annual'      => 'primary',
            'quarterly'   => 'success',
            'pre_board'   => 'danger',
            'other'       => 'secondary',
        ];
    }
}