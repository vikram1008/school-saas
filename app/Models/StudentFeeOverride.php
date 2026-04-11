<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentFeeOverride extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'student_fee_overrides';

    protected $fillable = [
        'student_profile_id',
        'fee_head_id',
        'academic_year_id',
        'amount',
        'reason',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class, 'fee_head_id');
    }
}