<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SaasSettings extends Model
{
    protected $connection = 'mysql'; // ← Always central DB

    protected $table = 'saas_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * In-memory store for the current request.
     *
     * WHY not Cache::remember()?
     *   stancl/tenancy wraps the default cache store with tags to isolate
     *   per-tenant data. Drivers like `file` and `database` don't support
     *   tagging, so Cache::remember() inside a tenant context throws:
     *   "This cache store does not support tagging."
     *
     *   saas_settings is central (not tenant-specific), so we bypass the
     *   Cache facade entirely and use a static property instead.
     *   This gives us request-level memoization with zero driver dependency.
     *   On high-traffic setups, swap to a tag-aware driver (Redis) and
     *   re-enable Cache::store('redis')->remember(...) if needed.
     */
    private static ?Collection $memo = null;

    /**
     * Get a setting value by key with optional default.
     * Memoized per-request (static property) — no cache driver required.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        // Load all settings once per request and keep in static property.
        if (static::$memo === null) {
            static::$memo = DB::connection('mysql')
                ->table('saas_settings')
                ->get()
                ->keyBy('key');
        }

        if (! static::$memo->has($key)) {
            return $default;
        }

        $setting = (object) static::$memo->get($key);

        return match ($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            default => $setting->value,
        };
    }

    /**
     * Set a setting value by key and invalidate the in-request memo.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );

        // Bust the in-request memo so subsequent get() calls see fresh data.
        static::$memo = null;
    }

    /**
     * Manually flush the in-request memo (e.g. after bulk updates).
     */
    public static function flushMemo(): void
    {
        static::$memo = null;
    }

    /**
     * Get all settings grouped by their group key.
     */
    public static function grouped(): array
    {
        return static::all()
            ->groupBy('group')
            ->toArray();
    }
}
