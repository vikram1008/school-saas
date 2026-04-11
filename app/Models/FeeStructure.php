<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'fee_structures';

    protected $fillable = [
        'academic_year_id',
        'class_id',
        'fee_head_id',
        'amount',
        'due_day',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'amount'    => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    public function feeHead()
    {
        return $this->belongsTo(FeeHead::class, 'fee_head_id');
    }
}