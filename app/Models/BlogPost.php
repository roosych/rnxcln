<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('sitemap.xml'));
        static::deleted(fn () => Cache::forget('sitemap.xml'));
    }

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'alt',
        'author_name',
        'meta_title',
        'meta_description',
        'og_image',
        'is_published',
        'is_featured',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /** Same services/-prefix-vs-plain-asset resolution as Service::imageUrl(). */
    public function coverImageUrl(): string
    {
        return $this->resolveUploadedUrl($this->cover_image);
    }

    public function ogImageUrl(): string
    {
        return $this->resolveUploadedUrl($this->og_image);
    }

    private function resolveUploadedUrl(?string $path): string
    {
        if (! $path) {
            return '';
        }

        return Str::startsWith($path, 'blog/')
            ? asset('storage/'.$path)
            : asset($path);
    }

    /**
     * Blog categories are the existing service catalog — no separate
     * taxonomy. Ordered by the pivot's own id so an admin's tick order
     * (first ticked = primary pill) survives a page reload.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'blog_post_service')
            ->orderBy('blog_post_service.id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /** Up to 4 other published posts, same category first, then latest overall. */
    public function relatedPosts(int $limit = 2): \Illuminate\Support\Collection
    {
        $categoryIds = $this->categories->pluck('id');

        return self::published()
            ->where('id', '!=', $this->id)
            ->with('categories')
            ->orderByRaw(
                $categoryIds->isNotEmpty()
                    ? 'exists (select 1 from blog_post_service where blog_post_service.blog_post_id = blog_posts.id and blog_post_service.service_id in ('.$categoryIds->implode(',').')) desc'
                    : '1'
            )
            ->orderByDesc('published_at')
            ->take($limit)
            ->get();
    }
}
