<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentBankDetail extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'student_bank_details';

    protected $fillable = [
        'student_profile_id',
        'bank_name',
        'bank_branch',
        'account_number',
        'ifsc_code',
        'account_holder',
        'account_holder_name',
    ];

    public function studentProfile()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }
}