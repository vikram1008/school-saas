<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class FeeDemand extends Model
{
    protected $connection = 'tenant';
    protected $table = 'fee_demands';

    protected $fillable = [
        'student_id', 'academic_year_id', 'fee_head_id',
        'demand_month', 'amount_due', 'amount_paid',
        'due_date', 'status', 'voided_at', 'void_reason',
    ];

    protected $casts = [
        'amount_due'  => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'due_date'    => 'date',
        'demand_month'=> 'date',
        'voided_at'   => 'datetime',
    ];

    // status: unpaid | partial | paid | waived | void
    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function feeHead(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FeeHead::class, 'fee_head_id');
    }

    public function academicYear(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function collectionItems(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeeCollectionItem::class, 'fee_demand_id');
    }

    public function getBalanceAttribute(): float
    {
        return (float)$this->amount_due - (float)$this->amount_paid;
    }
}