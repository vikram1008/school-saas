<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveApplication extends Model
{
    protected $connection = 'tenant';

    protected $table = 'leave_applications';

    protected $fillable = [
        'applicant_type',
        'applicant_id',
        'user_id',
        'applied_by_parent',
        'leave_type_id',
        'from_date',
        'to_date',
        'total_days',
        'reason',
        'document_path',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'reviewed_at' => 'datetime',
        'applied_by_parent' => 'boolean',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(TenantUser::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(TenantUser::class, 'reviewed_by');
    }

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'applicant_id');
    }

    public function staffProfile(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'applicant_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForStudents($query)
    {
        return $query->where('applicant_type', 'student');
    }

    public function scopeForStaff($query)
    {
        return $query->where('applicant_type', 'staff');
    }

    // ── Helpers ────────────────────────────────────────────────────

    /**
     * Compute total days (inclusive, weekdays only approximation).
     */
    public static function computeDays(string $fromDate, string $toDate): int
    {
        return max(1, Carbon::parse($fromDate)->diffInDays(Carbon::parse($toDate)) + 1);
    }

    /**
     * Get the resolved applicant profile (student or staff).
     */
    public function getApplicantAttribute(): StudentProfile|StaffProfile|null
    {
        return $this->applicant_type === 'student'
            ? $this->studentProfile
            : $this->staffProfile;
    }

    /** @return array<string, string> */
    public static function statusColors(): array
    {
        return [
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'cancelled' => 'secondary',
        ];
    }

    /** @return array<string, string> */
    public static function statusLabels(): array
    {
        return [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            'cancelled' => 'Cancelled',
        ];
    }

    public function statusColor(): string
    {
        return static::statusColors()[$this->status] ?? 'secondary';
    }

    public function statusLabel(): string
    {
        return static::statusLabels()[$this->status] ?? ucfirst($this->status);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function canBeCancelledBy(TenantUser $user): bool
    {
        return $this->isPending() && $this->user_id === $user->id;
    }
}
