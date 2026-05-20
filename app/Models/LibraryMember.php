<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LibraryMember extends Model
{
    protected $connection = 'tenant';

    protected $table = 'library_members';

    protected $fillable = [
        'member_type',
        'user_id',
        'profile_id',
        'member_number',
        'membership_start',
        'membership_expiry',
        'max_books_allowed',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'membership_start' => 'date',
        'membership_expiry' => 'date',
        'max_books_allowed' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function studentProfile(): BelongsTo
    {
        return $this->belongsTo(StudentProfile::class, 'profile_id');
    }

    public function staffProfile(): BelongsTo
    {
        return $this->belongsTo(StaffProfile::class, 'profile_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'member_id');
    }

    public function activeIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'member_id')
            ->whereIn('status', ['issued', 'overdue']);
    }

    // ── Helpers ────────────────────────────────────────────────────

    /**
     * Resolve the display name of this member regardless of type.
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->member_type === 'student' && $this->studentProfile) {
            return $this->studentProfile->full_name;
        }

        if ($this->member_type === 'staff' && $this->staffProfile) {
            return $this->staffProfile->full_name;
        }

        return 'Unknown Member';
    }

    /**
     * How many books the member currently has issued (not returned).
     */
    public function currentIssueCount(): int
    {
        return $this->activeIssues()->count();
    }

    public function canBorrow(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->membership_expiry && $this->membership_expiry->isPast()) {
            return false;
        }

        return $this->currentIssueCount() < $this->max_books_allowed;
    }

    /**
     * Generate the next member number.
     */
    public static function nextMemberNumber(): string
    {
        $max = static::max('member_number');

        if (! $max) {
            return 'LIB-'.date('Y').'-001';
        }

        if (preg_match('/(\d+)$/', $max, $m)) {
            $next = (int) $m[1] + 1;

            return 'LIB-'.date('Y').'-'.str_pad($next, 3, '0', STR_PAD_LEFT);
        }

        return 'LIB-'.date('Y').'-001';
    }
}
