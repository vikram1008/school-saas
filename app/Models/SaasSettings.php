<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SaasSettings extends Model
{
    protected $connection = 'mysql'; // ← Always central DB
    protected $table      = 'saas_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Get a setting value by key with optional default.
     * Cached for 1 hour to avoid repeated DB hits.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
         // Force central DB connection — saas_settings is never tenant-specific
        $settings = \Illuminate\Support\Facades\DB::connection('mysql')
            ->table('saas_settings')
            ->get()
            ->keyBy('key');

        $settings = collect($settings);

        if (!$settings->has($key)) {
            return $default;
        }

        $setting = (object) $settings->get($key);

        return match ($setting->type) {
            'integer' => (int) $setting->value,
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            default => $setting->value,
        };
    }

    /**
     * Set a setting value by key and bust cache.
     */
    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );

        Cache::forget('saas_settings'); // ← simple forget, no tags
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