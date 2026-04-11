<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeDemand extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'fee_demands';

    protected $fillable = [
        'student_profile_id',
        'fee_head_id',
        'academic_year_id',
        'period_label',
        'period_start',
        'period_end',
        'due_date',
        'amount_due',
        'amount_paid',
        'balance',
        'fine_amount',
        'status',
        'waive_reason',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'due_date'     => 'date',
        'amount_due'   => 'decimal:2',
        'amount_paid'  => 'decimal:2',
        'balance'      => 'decimal:2',
        'fine_amount'  => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class, 'fee_head_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function collectionItems()
    {
        return $this->hasMany(FeeCollectionItem::class, 'fee_demand_id');
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return $this->status === 'overdue'
            || ($this->status === 'pending' && $this->due_date->isPast());
    }
}