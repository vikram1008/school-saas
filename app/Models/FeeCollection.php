<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeCollection extends Model
{
    protected $connection = 'tenant';
    protected $table      = 'fee_collections';

    protected $fillable = [
        'receipt_number',
        'student_profile_id',
        'collected_by',
        'payment_mode',
        'payment_reference',
        'total_amount',
        'collection_date',
        'notes',
    ];

    protected $casts = [
        'total_amount'    => 'decimal:2',
        'collection_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(StudentProfile::class, 'student_profile_id');
    }

    public function collectedBy()
    {
        return $this->belongsTo(TenantUser::class, 'collected_by');
    }

    public function items()
    {
        return $this->hasMany(FeeCollectionItem::class, 'fee_collection_id');
    }

    public static function paymentModeLabels(): array
    {
        return [
            'cash'          => 'Cash / नकद',
            'upi'           => 'UPI',
            'bank_transfer' => 'Bank Transfer / बैंक हस्तांतरण',
            'cheque'        => 'Cheque / चेक',
            'dd'            => 'Demand Draft (DD)',
            'online'        => 'Online / ऑनलाइन',
        ];
    }

    // Generate unique receipt number
    public static function generateReceiptNumber(): string
    {
        $prefix = 'RCP-' . date('Ym') . '-';
        $last   = static::where('receipt_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->first();

        $next = $last
            ? (int) substr($last->receipt_number, strlen($prefix)) + 1
            : 1;

        return $prefix . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}