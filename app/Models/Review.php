<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_source_id',
        'external_id',
        'author_name',
        'author_avatar',
        'rating',
        'title',
        'content',
        'location',
        'review_date',
        'reply',
        'reply_date',
        'source_url',
        'published',
        'featured',
        'verified',
        'metadata',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'review_date' => 'date',
            'reply_date' => 'datetime',
            'published' => 'boolean',
            'featured' => 'boolean',
            'verified' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(ReviewSource::class, 'review_source_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('published', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function isManual(): bool
    {
        return $this->external_id === null;
    }

    public function authorAvatarUrl(): ?string
    {
        if (! $this->author_avatar) {
            return null;
        }

        // Imported avatars are stored as absolute URLs from the source
        // (Google/Yelp/Facebook profile photos); manual uploads are stored
        // as a relative path on the public disk.
        if (str_starts_with($this->author_avatar, 'http://') || str_starts_with($this->author_avatar, 'https://')) {
            return $this->author_avatar;
        }

        return asset('storage/'.$this->author_avatar);
    }
}
