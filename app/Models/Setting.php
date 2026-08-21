<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.all'));
        static::deleted(fn () => Cache::forget('settings.all'));
    }

    /** group.key => value map for every row, cached until the next write. */
    public static function allCached(): array
    {
        return Cache::rememberForever('settings.all', function () {
            return static::query()->get()->mapWithKeys(
                fn (self $setting) => ["{$setting->group}.{$setting->key}" => $setting->value]
            )->all();
        });
    }

    public static function get(string $group, string $key, mixed $default = null): mixed
    {
        return static::allCached()["{$group}.{$key}"] ?? $default;
    }

    public static function put(string $group, string $key, mixed $value): void
    {
        static::updateOrCreate(compact('group', 'key'), ['value' => $value]);
    }
}
