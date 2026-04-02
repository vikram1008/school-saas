<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table      = 'sections';

    protected $fillable = [
        'class_id',
        'name',
        'class_teacher_id',
        'capacity',
        'order',
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function classTeacher()
    {
        return $this->belongsTo(TenantUser::class, 'class_teacher_id');
    }

    public function subjectTeachers()
    {
        return $this->hasMany(SectionSubjectTeacher::class, 'section_id');
    }

    public function students()
    {
        return $this->hasMany(StudentProfile::class, 'section_id');
    }

    public function getStudentCountAttribute(): int
    {
        return $this->students()->count();
    }

    public function getFullNameAttribute(): string
    {
        return $this->class->name . ' - ' . $this->name;
    }
}