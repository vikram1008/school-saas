<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAddress extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'staff_addresses';

    protected $fillable = [
        'staff_profile_id',
        'perm_house_no', 'perm_house_no_hi',
        'perm_street', 'perm_street_hi',
        'perm_village_city', 'perm_village_city_hi',
        'perm_tehsil', 'perm_tehsil_hi',
        'perm_district', 'perm_district_hi',
        'perm_state', 'perm_state_hi',
        'perm_pincode',
        'same_as_permanent',
        'curr_house_no', 'curr_street',
        'curr_village_city', 'curr_tehsil',
        'curr_district', 'curr_state',
        'curr_pincode',
    ];

    protected $casts = [
        'same_as_permanent' => 'boolean',
    ];

    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_profile_id');
    }
}