<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParentStudentLink extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'parent_student_links';

    protected $fillable = [
        'parent_profile_id',
        'student_profile_id',
        'relationship',
        'is_primary',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(ParentProfile::class, 'parent_profile_id');
    }

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }
}