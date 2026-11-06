<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Throwable;

class Setting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['key', 'value'];

    /** All settings: config defaults overridden by the values saved in the admin panel. */
    public static function allValues(): array
    {
        $defaults = config('autostvabaks.settings', []);

        try {
            $stored = Cache::rememberForever('site.settings', fn () => static::query()->pluck('value', 'key')->all());
        } catch (Throwable) {
            $stored = []; // e.g. before the first migration
        }

        return array_merge($defaults, array_map(fn ($v) => (string) $v, $stored));
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        return static::allValues()[$key] ?? $default;
    }

    public static function putMany(array $values): void
    {
        foreach ($values as $key => $value) {
            static::query()->updateOrCreate(['key' => $key], ['value' => (string) ($value ?? '')]);
        }

        Cache::forget('site.settings');
    }
}
