<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'grade_scales';

    protected $fillable = [
        'academic_year_id', 'grade',
        'min_percentage', 'max_percentage',
        'grade_point', 'description',
        'description_hi', 'color',
    ];

    protected $casts = [
        'min_percentage' => 'decimal:2',
        'max_percentage' => 'decimal:2',
        'grade_point'    => 'decimal:2',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get grade for a given percentage
     */
    public static function getGrade(float $percentage, int $academicYearId): ?self
    {
        return static::where('academic_year_id', $academicYearId)
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->first();
    }

    public static function defaultCbseScales(): array
    {
        return [
            ['grade' => 'A1', 'min' => 91, 'max' => 100, 'point' => 10.0, 'desc' => 'Outstanding',  'desc_hi' => 'उत्कृष्ट',   'color' => 'success'],
            ['grade' => 'A2', 'min' => 81, 'max' => 90,  'point' => 9.0,  'desc' => 'Excellent',    'desc_hi' => 'अति उत्तम',  'color' => 'success'],
            ['grade' => 'B1', 'min' => 71, 'max' => 80,  'point' => 8.0,  'desc' => 'Very Good',    'desc_hi' => 'बहुत अच्छा', 'color' => 'primary'],
            ['grade' => 'B2', 'min' => 61, 'max' => 70,  'point' => 7.0,  'desc' => 'Good',         'desc_hi' => 'अच्छा',      'color' => 'primary'],
            ['grade' => 'C1', 'min' => 51, 'max' => 60,  'point' => 6.0,  'desc' => 'Average',      'desc_hi' => 'औसत',        'color' => 'info'],
            ['grade' => 'C2', 'min' => 41, 'max' => 50,  'point' => 5.0,  'desc' => 'Below Average','desc_hi' => 'औसत से कम',  'color' => 'warning'],
            ['grade' => 'D',  'min' => 33, 'max' => 40,  'point' => 4.0,  'desc' => 'Pass',         'desc_hi' => 'उत्तीर्ण',   'color' => 'warning'],
            ['grade' => 'E',  'min' => 0,  'max' => 32,  'point' => 0.0,  'desc' => 'Fail',         'desc_hi' => 'अनुत्तीर्ण', 'color' => 'danger'],
        ];
    }
}