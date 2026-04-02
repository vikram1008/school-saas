<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $connection = 'mysql'; // ← Always central DB
    protected $table      = 'subscriptions';
    
    protected $fillable = [
        'tenant_id',
        'billing_cycle',
        'period_start',
        'period_end',
        'due_date',
        'student_count_snapshot',
        'per_student_rate',
        'amount_due',
        'status',
        'amount_paid',
        'paid_at',
        'payment_reference',
        'days_overdue',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end'   => 'date',
        'due_date'     => 'date',
        'paid_at'      => 'datetime',
        'amount_due'   => 'decimal:2',
        'amount_paid'  => 'decimal:2',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    // Helpers
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isOverdue(): bool
    {
        return in_array($this->status, ['grace_warning', 'grace_readonly', 'suspended']);
    }

    public function daysUntilDue(): int
    {
        return now()->diffInDays($this->due_date, false);
    }
}