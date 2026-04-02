<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentProfile extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table      = 'student_profiles';

    protected $fillable = [
        'user_id',
        'admission_number',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'photo',
        'phone',
        'academic_year_id',
        'class_id',
        'section_id',
        'admission_year',
        'status',
        'address',
        'city',
        'state',
        'pincode',
        'parent_profile_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}