<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffProfile extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table      = 'staff_profiles';

    protected $fillable = [
        'user_id',
        'staff_type',
        'employee_code',
        'first_name', 'first_name_hi',
        'last_name', 'last_name_hi',
        'date_of_birth',
        'gender',
        'blood_group',
        'photo',
        'phone',
        'whatsapp',
        'email',
        'aadhaar_number',
        'pan_number',
        'id_proof_type',
        'id_proof_number',
        'designation', 'designation_hi',
        'department', 'department_hi',
        'qualification', 'qualification_hi',
        'experience_years',
        'joining_date',
        'employment_type',
        'status',
        'salary',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date'  => 'date',
        'salary'        => 'decimal:2',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function address()
    {
        return $this->hasOne(StaffAddress::class, 'staff_profile_id');
    }

    public function documents()
    {
        return $this->hasMany(StaffDocument::class, 'staff_profile_id');
    }

    public function subjectAssignments()
    {
        return $this->hasMany(StaffSubjectAssignment::class, 'staff_profile_id');
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

    public function isTeacher(): bool
    {
        return $this->staff_type === 'teaching';
    }

    public static function typeLabels(): array
    {
        return [
            'teaching'       => 'Teaching / शिक्षण',
            'non_teaching'   => 'Non-Teaching / गैर-शिक्षण',
            'administrative' => 'Administrative / प्रशासनिक',
        ];
    }

    public static function designations(): array
    {
        return [
            'teaching'       => [
                'PRT'              => 'Primary Teacher (PRT)',
                'TGT'              => 'Trained Graduate Teacher (TGT)',
                'PGT'              => 'Post Graduate Teacher (PGT)',
                'head_teacher'     => 'Head Teacher',
                'teacher'          => 'Teacher',
            ],
            'non_teaching'   => [
                'accountant'  => 'Accountant',
                'librarian'   => 'Librarian',
                'clerk'       => 'Clerk',
                'peon'        => 'Peon',
                'guard'       => 'Guard',
                'lab_assistant' => 'Lab Assistant',
                'computer_operator' => 'Computer Operator',
            ],
            'administrative' => [
                'principal'       => 'Principal',
                'vice_principal'  => 'Vice Principal',
                'school_admin'    => 'School Administrator',
                'coordinator'     => 'Coordinator',
            ],
        ];
    }
}