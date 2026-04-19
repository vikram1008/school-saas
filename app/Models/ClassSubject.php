<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'class_subjects';

    protected $fillable = [
        'class_id',
        'subject_name',
        'subject_name_hi',
        'teacher_id',
        'periods_per_week',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(StaffProfile::class, 'teacher_id');
    }
}