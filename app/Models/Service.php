<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Service extends Model
{
    /**
     * The folders services are organized into. The keys are fixed, not
     * admin-editable — each one is tied to a specific spot in
     * resources/views/pages/home.blade.php and services.blade.php, so a new
     * folder would need a developer to add markup for it. The display names
     * are editable per-key (see folderNames()); these are just the defaults
     * shown until an admin renames one.
     */
    public const DEFAULT_FOLDERS = [
        'core' => 'Carpet & Upholstery Cleaning',
        'home-office' => 'Home & Office Cleaning',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('sitemap.xml'));
        static::deleted(fn () => Cache::forget('sitemap.xml'));
    }

    protected $fillable = [
        'section',
        'title',
        'slug',
        'tagline',
        'text',
        'image',
        'alt',
        'items',
        'link_type',
        'link_url',
        'meta_title',
        'meta_description',
        'before_image',
        'after_image',
        'sort_order',
        'is_active',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    /** Folder display names — defaults, overridden per-key by an admin rename. */
    public static function folderNames(): array
    {
        $overrides = array_filter((array) Setting::get('site', 'service_folders', []));

        return array_merge(self::DEFAULT_FOLDERS, $overrides);
    }

    /**
     * Images uploaded through the admin are stored on the `public` disk
     * (path prefixed `services/`); images seeded from the old config file
     * are plain `public/` asset paths. Resolve either to a browsable URL.
     * Used for both "long" card photos and "icon" category icons — same
     * `image`/`alt` columns, just a different label in the admin form.
     */
    public function imageUrl(): string
    {
        return $this->resolveUploadedUrl($this->image);
    }

    public function beforeImageUrl(): string
    {
        return $this->resolveUploadedUrl($this->before_image);
    }

    public function afterImageUrl(): string
    {
        return $this->resolveUploadedUrl($this->after_image);
    }

    private function resolveUploadedUrl(?string $path): string
    {
        if (! $path) {
            return '';
        }

        return Str::startsWith($path, 'services/')
            ? asset('storage/'.$path)
            : asset($path);
    }

    /** "How we clean it" steps owned by this service. */
    public function steps(): HasMany
    {
        return $this->hasMany(ProcessStep::class)->orderBy('sort_order');
    }

    /** Active services in a given Home/Services page section, in admin order. */
    public function scopeSection(Builder $query, string $section): Builder
    {
        return $query->where('section', $section)
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    /** The admin-curated "top picks" shown on the homepage, in admin order. */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Where a card for this service should link to. `contact` and `custom`
     * are fixed destinations; the default (`page`) is this service's own
     * detail page — every service has one now, so there's no more
     * per-calling-page fallback to thread through.
     */
    public function url(): string
    {
        return match ($this->link_type) {
            'contact' => route('contact'),
            'custom' => $this->link_url ?: route('services.show', $this),
            default => route('services.show', $this),
        };
    }
}
