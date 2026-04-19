<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentProfile extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table      = 'parent_profiles';

    protected $fillable = [
        'user_id',
        'first_name', 'first_name_hi',
        'last_name', 'last_name_hi',
        'relation',
        'phone', 'mobile', 'email',
        'alternate_phone',
        'occupation', 'occupation_hi',
        'photo',
        'address', 'city', 'state', 'pincode',
        'id_proof_type', 'id_proof_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function studentLinks()
    {
        return $this->hasMany(ParentStudentLink::class, 'parent_profile_id');
    }

    public function students()
    {
        return $this->belongsToMany(
            StudentProfile::class,
            'parent_student_links',
            'parent_profile_id',
            'student_profile_id'
        )->withPivot('relationship', 'is_primary')->withTimestamps();
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFullNameHiAttribute(): string
    {
        return ($this->first_name_hi ?? $this->first_name)
             . ' '
             . ($this->last_name_hi ?? $this->last_name);
    }
}