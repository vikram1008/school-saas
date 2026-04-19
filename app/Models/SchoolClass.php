<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table      = 'classes';

    protected $fillable = [
        'academic_year_id',
        'name',
        'order',
        'has_sections',
        'class_teacher_id',
        'capacity',
        'description',
    ];

    protected $casts = [
        'has_sections' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'class_id')->orderBy('order');
    }

    public function classTeacher()
    {
        return $this->belongsTo(TenantUser::class, 'class_teacher_id');
    }

    public function subjectTeachers()
    {
        return $this->hasMany(SectionSubjectTeacher::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(StudentProfile::class, 'class_id');
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }

    // Auto-assign next order number
    public static function nextOrder(int $academicYearId): int
    {
        return (static::where('academic_year_id', $academicYearId)->max('order') ?? 0) + 1;
    }

    public function subjects()
    {
        return $this->hasMany(ClassSubject::class, 'class_id')
            ->orderBy('sort_order');
    }
}