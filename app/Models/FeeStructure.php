<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $connection = 'tenant';
    protected $table = 'fee_structures';

    protected $fillable = [
        'academic_year_id', 'school_class_id', 'fee_head_id', 'amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function feeHead(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FeeHead::class, 'fee_head_id');
    }

    public function schoolClass(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'school_class_id');
    }

    public function academicYear(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }
}