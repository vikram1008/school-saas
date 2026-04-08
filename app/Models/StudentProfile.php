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
        'sr_number',
        'admission_date',
        'first_name', 'first_name_hi',
        'last_name', 'last_name_hi',
        'date_of_birth',
        'dob_in_words', 'dob_in_words_hi',
        'aadhaar_number',
        'jan_aadhaar_number',
        'gender',
        'category',
        'minority_status',
        'bpl_status',
        'cwsn_type',
        'blood_group',
        'identification_mark', 'identification_mark_hi',
        'photo',
        'phone',
        'whatsapp',
        'email',
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
        'date_of_birth'    => 'date',
        'admission_date'   => 'date',
        'minority_status'  => 'boolean',
        'bpl_status'       => 'boolean',
    ];

    // Relationships
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

    public function familyDetail()
    {
        return $this->hasOne(StudentFamilyDetail::class, 'student_profile_id');
    }

    public function address()
    {
        return $this->hasOne(StudentAddress::class, 'student_profile_id');
    }

    public function academicHistory()
    {
        return $this->hasOne(StudentAcademicHistory::class, 'student_profile_id');
    }

    public function bankDetail()
    {
        return $this->hasOne(StudentBankDetail::class, 'student_profile_id');
    }

    public function subjects()
    {
        return $this->hasOne(StudentSubject::class, 'student_profile_id');
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class, 'student_profile_id');
    }

    // Helpers
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

    public function getClassSectionAttribute(): string
    {
        $class   = $this->class?->name ?? '—';
        $section = $this->section?->name;
        return $section ? "{$class} - {$section}" : $class;
    }
}