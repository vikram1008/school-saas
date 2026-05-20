<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LibraryBook extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $table = 'library_books';

    protected $fillable = [
        'accession_number',
        'title',
        'title_hi',
        'author',
        'publisher',
        'isbn',
        'category',
        'publication_year',
        'edition',
        'language',
        'total_copies',
        'available_copies',
        'rack_location',
        'price',
        'description',
        'is_reference_only',
        'is_active',
    ];

    protected $casts = [
        'is_reference_only' => 'boolean',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
        'publication_year' => 'integer',
    ];

    // ── Relationships ──────────────────────────────────────────────

    public function issues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'book_id');
    }

    public function activeIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class, 'book_id')
            ->whereIn('status', ['issued', 'overdue']);
    }

    // ── Scopes ─────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_copies', '>', 0)
            ->where('is_reference_only', false)
            ->where('is_active', true);
    }

    // ── Helpers ────────────────────────────────────────────────────

    /** @return array<string, string> */
    public static function categories(): array
    {
        return [
            'textbook' => 'Textbook',
            'fiction' => 'Fiction',
            'non-fiction' => 'Non-Fiction',
            'reference' => 'Reference',
            'magazine' => 'Magazine / Periodical',
            'science' => 'Science',
            'history' => 'History',
            'biography' => 'Biography',
            'general' => 'General',
        ];
    }

    /** @return array<string, string> */
    public static function languages(): array
    {
        return [
            'english' => 'English',
            'hindi' => 'Hindi',
            'both' => 'English & Hindi',
            'other' => 'Other',
        ];
    }

    public function isAvailable(): bool
    {
        return $this->available_copies > 0 && ! $this->is_reference_only && $this->is_active;
    }

    /**
     * Generate the next accession number based on current max.
     */
    public static function nextAccessionNumber(): string
    {
        $max = static::withTrashed()->max('accession_number');

        if (! $max) {
            return 'ACC-'.date('Y').'-0001';
        }

        // Extract numeric part from ACC-YYYY-NNNN
        if (preg_match('/(\d+)$/', $max, $m)) {
            $next = (int) $m[1] + 1;

            return 'ACC-'.date('Y').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
        }

        return 'ACC-'.date('Y').'-0001';
    }
}
