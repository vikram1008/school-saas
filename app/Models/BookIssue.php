<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookIssue extends Model
{
    protected $connection = 'tenant';

    protected $table = 'book_issues';

    protected $fillable = [
        'issue_number',
        'book_id',
        'member_id',
        'issued_by',
        'issue_date',
        'due_date',
        'return_date',
        'returned_to',
        'status',
        'fine_amount',
        'fine_paid',
        'fine_per_day',
        'notes',
        'condition_on_issue',
        'condition_on_return',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
        'fine_amount' => 'decimal:2',
        'fine_paid' => 'decimal:2',
        'fine_per_day' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function book(): BelongsTo
    {
        return $this->belongsTo(LibraryBook::class, 'book_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(LibraryMember::class, 'member_id');
    }

    public function issuedBy(): BelongsTo
    {
        return $this->belongsTo(TenantUser::class, 'issued_by');
    }

    public function returnedTo(): BelongsTo
    {
        return $this->belongsTo(TenantUser::class, 'returned_to');
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['issued', 'overdue']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'overdue')
            ->orWhere(function ($q) {
                $q->where('status', 'issued')
                    ->where('due_date', '<', today());
            });
    }

    // ── Helpers ────────────────────────────────────────────────────

    /**
     * Calculate the current outstanding fine based on overdue days.
     * Fine is only charged on unreturned overdue books.
     */
    public function calculateFine(): float
    {
        if ($this->status === 'returned') {
            return (float) $this->fine_amount;
        }

        $checkDate = $this->return_date ?? today();
        $overdueDays = max(0, $this->due_date->diffInDays($checkDate, false));

        return $overdueDays * $this->fine_per_day;
    }

    public function fineDue(): float
    {
        return max(0, $this->calculateFine() - (float) $this->fine_paid);
    }

    public function isOverdue(): bool
    {
        if ($this->status === 'returned') {
            return false;
        }

        return today()->greaterThan($this->due_date);
    }

    public function overdueDays(): int
    {
        if (! $this->isOverdue()) {
            return 0;
        }

        return (int) abs(today()->diffInDays($this->due_date));
    }

    /**
     * Generate the next issue number.
     */
    public static function nextIssueNumber(): string
    {
        $max = static::max('issue_number');

        if (! $max) {
            return 'ISS-'.date('Y').'-00001';
        }

        if (preg_match('/(\d+)$/', $max, $m)) {
            $next = (int) $m[1] + 1;

            return 'ISS-'.date('Y').'-'.str_pad($next, 5, '0', STR_PAD_LEFT);
        }

        return 'ISS-'.date('Y').'-00001';
    }

    /**
     * Status badge color mapping.
     *
     * @return array<string, string>
     */
    public static function statusColors(): array
    {
        return [
            'issued' => 'primary',
            'returned' => 'success',
            'overdue' => 'danger',
            'lost' => 'dark',
        ];
    }
}
