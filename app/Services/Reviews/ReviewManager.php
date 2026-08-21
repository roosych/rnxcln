<?php

namespace App\Services\Reviews;

use App\Models\Review;
use App\Models\ReviewSource;
use App\Services\Reviews\Providers\FacebookReviewsProvider;
use App\Services\Reviews\Providers\GoogleReviewsProvider;
use App\Services\Reviews\Providers\ManualReviewProvider;
use App\Services\Reviews\Providers\YelpReviewsProvider;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * The only thing controllers and jobs talk to for review sourcing.
 * Resolves the right provider for a source, runs the sync, and upserts the
 * normalized results — providers never touch the `reviews` table directly.
 */
class ReviewManager
{
    /** @var array<string, class-string<ReviewProviderInterface>> */
    private array $providers = [
        ReviewSource::PROVIDER_MANUAL => ManualReviewProvider::class,
        ReviewSource::PROVIDER_GOOGLE => GoogleReviewsProvider::class,
        ReviewSource::PROVIDER_YELP => YelpReviewsProvider::class,
        ReviewSource::PROVIDER_FACEBOOK => FacebookReviewsProvider::class,
    ];

    public function providerFor(ReviewSource $source): ReviewProviderInterface
    {
        $class = $this->providers[$source->provider] ?? null;

        if (! $class) {
            throw new InvalidArgumentException("No provider registered for '{$source->provider}'.");
        }

        return app($class);
    }

    public function testConnection(ReviewSource $source): ConnectionTestResult
    {
        return $this->providerFor($source)->testConnection($source);
    }

    public function sync(ReviewSource $source): SyncResult
    {
        $source->forceFill(['sync_status' => ReviewSource::STATUS_SYNCING])->save();

        $result = new SyncResult;

        try {
            $normalized = $this->providerFor($source)->fetchReviews($source);

            foreach ($normalized as $item) {
                $this->upsert($source, $item, $result);
            }

            $source->forceFill([
                'sync_status' => ReviewSource::STATUS_SUCCESS,
                'sync_error' => null,
                'last_synced_at' => now(),
            ])->save();
        } catch (ProviderUnsupportedException $e) {
            $result->markUnsupported($e->getMessage());
            $source->forceFill([
                'sync_status' => ReviewSource::STATUS_UNSUPPORTED,
                'sync_error' => $e->getMessage(),
            ])->save();
        } catch (ProviderSyncException $e) {
            $result->failed($e->getMessage());
            $source->forceFill([
                'sync_status' => ReviewSource::STATUS_ERROR,
                'sync_error' => $e->getMessage(),
            ])->save();
            Log::warning('Review source sync failed', ['source' => $source->provider, 'error' => $e->getMessage()]);
        }

        return $result;
    }

    /** @return array<string, SyncResult> keyed by provider slug */
    public function syncAllEnabled(): array
    {
        $results = [];

        foreach (ReviewSource::query()->enabled()->connected()->get() as $source) {
            $results[$source->provider] = $this->sync($source);
        }

        return $results;
    }

    private function upsert(ReviewSource $source, NormalizedReview $item, SyncResult $result): void
    {
        $existing = Review::query()
            ->where('review_source_id', $source->id)
            ->where('external_id', $item->externalId)
            ->first();

        $attributes = [
            'author_name' => $item->authorName,
            'author_avatar' => $item->authorAvatar,
            'rating' => $item->rating,
            'title' => $item->title,
            'content' => $item->content,
            'review_date' => $item->reviewDate,
            'reply' => $item->reply,
            'reply_date' => $item->replyDate,
            'source_url' => $item->sourceUrl,
            'metadata' => $item->metadata,
        ];

        if (! $existing) {
            Review::create($attributes + [
                'review_source_id' => $source->id,
                'external_id' => $item->externalId,
                // Imported reviews are real customer feedback — publish
                // them by default so a sync doesn't silently do nothing.
                'published' => true,
            ]);
            $result->created();

            return;
        }

        $changed = collect($attributes)->some(
            fn ($value, $key) => $this->normalizeForCompare($existing->{$key}) !== $this->normalizeForCompare($value)
        );

        if (! $changed) {
            $result->skipped();

            return;
        }

        $existing->update($attributes);
        $result->updated();
    }

    private function normalizeForCompare(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        return $value;
    }
}
