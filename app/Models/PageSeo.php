<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageSeo extends Model
{
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('sitemap.xml'));
    }

    protected $fillable = [
        'route_name',
        'meta_title',
        'meta_description',
        'og_image',
        'canonical_url',
        'robots',
        'schema_json',
    ];

    protected function casts(): array
    {
        return [
            'schema_json' => 'array',
        ];
    }

    public static function forRoute(string $routeName): ?self
    {
        return static::query()->where('route_name', $routeName)->first();
    }
}
