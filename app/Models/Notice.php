<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    use SoftDeletes;

    protected $connection = 'tenant';
    protected $table      = 'notices';

    protected $fillable = [
        'title', 'title_hi',
        'content', 'content_hi',
        'visible_to',
        'published_by',
        'published_at',
        'expires_at',
        'is_published',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
        'is_published' => 'boolean',
    ];

    public function publishedBy()
    {
        return $this->belongsTo(TenantUser::class, 'published_by');
    }

    public function isActive(): bool
    {
        return $this->is_published
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public static function visibleToLabels(): array
    {
        return [
            'all'      => 'Everyone / सभी',
            'parents'  => 'Parents Only / अभिभावक',
            'staff'    => 'Staff Only / स्टाफ',
            'students' => 'Students Only / छात्र',
        ];
    }
}