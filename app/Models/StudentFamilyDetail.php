<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFamilyDetail extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'student_family_details';

    protected $fillable = [
        'student_profile_id',
        'father_name', 'father_name_hi',
        'father_occupation', 'father_occupation_hi',
        'father_annual_income',
        'father_mobile', 'father_aadhaar',
        'mother_name', 'mother_name_hi',
        'mother_occupation', 'mother_occupation_hi',
        'mother_annual_income',
        'mother_mobile', 'mother_aadhaar',
        'guardian_name', 'guardian_name_hi',
        'guardian_relationship', 'guardian_relationship_hi',
        'guardian_mobile',
        'guardian_occupation', 'guardian_occupation_hi',
        'guardian_annual_income',
    ];

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }
}