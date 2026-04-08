<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffSubjectAssignment extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'staff_subject_assignments';

    protected $fillable = [
        'staff_profile_id',
        'class_id',
        'section_id',
        'subject_name',
        'subject_name_hi',
    ];

    public function staffProfile()
    {
        return $this->belongsTo(StaffProfile::class, 'staff_profile_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }
}