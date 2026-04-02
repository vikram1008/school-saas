<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionSubjectTeacher extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'section_subject_teachers';

    protected $fillable = [
        'class_id',
        'section_id',
        'user_id',
        'subject_name',
    ];

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id');
    }

    public function teacher()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }
}