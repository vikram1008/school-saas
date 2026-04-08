<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAddress extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'student_addresses';

    protected $fillable = [
        'student_profile_id',
        'perm_house_no', 'perm_house_no_hi',
        'perm_street', 'perm_street_hi',
        'perm_village_city', 'perm_village_city_hi',
        'perm_tehsil', 'perm_tehsil_hi',
        'perm_district', 'perm_district_hi',
        'perm_state', 'perm_state_hi',
        'perm_pincode',
        'same_as_permanent',
        'corr_house_no', 'corr_street',
        'corr_village_city', 'corr_tehsil',
        'corr_district', 'corr_state',
        'corr_pincode',
    ];

    protected $casts = [
        'same_as_permanent' => 'boolean',
    ];

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }
}