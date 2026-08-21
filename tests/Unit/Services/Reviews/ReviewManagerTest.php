<?php

namespace Tests\Unit\Services\Reviews;

use App\Models\Review;
use App\Models\ReviewSource;
use App\Services\Reviews\NormalizedReview;
use App\Services\Reviews\Providers\GoogleReviewsProvider;
use App\Services\Reviews\ReviewManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakeReviewProvider;
use Tests\TestCase;

class ReviewManagerTest extends TestCase
{
    use RefreshDatabase;

    private function bindFakeGoogleProvider(): FakeReviewProvider
    {
        $fake = new FakeReviewProvider;
        $this->app->instance(GoogleReviewsProvider::class, $fake);

        return $fake;
    }

    /** The seed migration already creates one row per built-in provider — reuse it rather than inserting a duplicate. */
    private function googleSource(): ReviewSource
    {
        $source = ReviewSource::query()->where('provider', ReviewSource::PROVIDER_GOOGLE)->firstOrFail();
        $source->update(['connected' => true, 'enabled' => true]);

        return $source;
    }

    private function yelpSource(): ReviewSource
    {
        return ReviewSource::query()->where('provider', ReviewSource::PROVIDER_YELP)->firstOrFail();
    }

    public function test_sync_creates_new_reviews_from_the_provider(): void
    {
        $fake = $this->bindFakeGoogleProvider();
        $source = $this->googleSource();

        $fake->reviews = [
            new NormalizedReview(externalId: 'g-1', authorName: 'Jane Doe', rating: 5, content: 'Great job.'),
            new NormalizedReview(externalId: 'g-2', authorName: 'John Roe', rating: 4, content: 'Pretty good.'),
        ];

        $result = app(ReviewManager::class)->sync($source);

        $this->assertSame(2, $result->created);
        $this->assertSame(0, $result->updated);
        $this->assertSame(0, $result->skipped);
        $this->assertSame(0, $result->errors);
        $this->assertSame(2, Review::query()->where('review_source_id', $source->id)->count());
        $this->assertSame(ReviewSource::STATUS_SUCCESS, $source->fresh()->sync_status);
        $this->assertNotNull($source->fresh()->last_synced_at);
    }

    public function test_sync_skips_unchanged_and_updates_changed_reviews_by_external_id(): void
    {
        $fake = $this->bindFakeGoogleProvider();
        $source = $this->googleSource();

        $fake->reviews = [new NormalizedReview(externalId: 'g-1', authorName: 'Jane Doe', rating: 5, content: 'Great job.')];
        app(ReviewManager::class)->sync($source);
        $this->assertSame(1, Review::count());

        // Same external_id, unchanged content -> skipped, no duplicate row.
        $result = app(ReviewManager::class)->sync($source);
        $this->assertSame(0, $result->created);
        $this->assertSame(0, $result->updated);
        $this->assertSame(1, $result->skipped);
        $this->assertSame(1, Review::count());

        // Same external_id, the reply changed on the source -> updates the existing row.
        $fake->reviews = [new NormalizedReview(externalId: 'g-1', authorName: 'Jane Doe', rating: 5, content: 'Great job.', reply: 'Thank you!')];
        $result = app(ReviewManager::class)->sync($source);
        $this->assertSame(0, $result->created);
        $this->assertSame(1, $result->updated);
        $this->assertSame(1, Review::count());
        $this->assertSame('Thank you!', Review::first()->reply);
    }

    public function test_sync_records_a_failed_api_call_without_crashing(): void
    {
        $fake = $this->bindFakeGoogleProvider();
        $fake->throwSyncException = true;
        $source = $this->googleSource();

        $result = app(ReviewManager::class)->sync($source);

        $this->assertSame(1, $result->errors);
        $this->assertTrue($result->hasErrors());
        $this->assertSame(ReviewSource::STATUS_ERROR, $source->fresh()->sync_status);
        $this->assertNotNull($source->fresh()->sync_error);
    }

    public function test_sync_marks_unsupported_when_the_provider_cannot_fetch_reviews(): void
    {
        $fake = $this->bindFakeGoogleProvider();
        $fake->throwUnsupported = true;
        $source = $this->googleSource();

        $result = app(ReviewManager::class)->sync($source);

        $this->assertTrue($result->unsupported);
        $this->assertSame(ReviewSource::STATUS_UNSUPPORTED, $source->fresh()->sync_status);
    }

    public function test_sync_all_enabled_only_touches_enabled_and_connected_sources(): void
    {
        $fake = $this->bindFakeGoogleProvider();
        $fake->reviews = [new NormalizedReview(externalId: 'g-1', authorName: 'Jane Doe', rating: 5, content: 'Great job.')];

        $enabled = $this->googleSource();
        $this->yelpSource()->update(['connected' => false, 'enabled' => false]);
        // Manual is enabled/connected by default too (it needs no external
        // connection) — disable it here to isolate this assertion to Google.
        ReviewSource::query()->where('provider', ReviewSource::PROVIDER_MANUAL)->update(['enabled' => false]);

        $results = app(ReviewManager::class)->syncAllEnabled();

        $this->assertArrayHasKey($enabled->provider, $results);
        $this->assertCount(1, $results);
    }
}
