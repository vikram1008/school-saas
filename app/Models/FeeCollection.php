<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class FeeCollection extends Model
{
    protected $connection = 'tenant';
    protected $table = 'fee_collections';

    protected $fillable = [
        'student_id', 'academic_year_id', 'receipt_number',
        'collected_by', 'payment_mode', 'reference_number',
        'total_amount', 'collected_at', 'remarks',
    ];

    protected $casts = [
        'total_amount'  => 'decimal:2',
        'collected_at'  => 'datetime',
    ];

    // payment_mode: cash | upi | bank_transfer | cheque | dd | online
    public static array $paymentModes = [
        'cash'          => 'Cash',
        'upi'           => 'UPI',
        'bank_transfer' => 'Bank Transfer',
        'cheque'        => 'Cheque',
        'dd'            => 'Demand Draft',
        'online'        => 'Online',
    ];

    public function student(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'student_id');
    }

    public function academicYear(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function collectedBy(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TenantUser::class, 'collected_by');
    }

    public function items(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeeCollectionItem::class, 'fee_collection_id');
    }
}