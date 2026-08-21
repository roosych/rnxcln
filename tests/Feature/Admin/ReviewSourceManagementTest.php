<?php

namespace Tests\Feature\Admin;

use App\Jobs\SyncReviewSourceJob;
use App\Models\Review;
use App\Models\ReviewSource;
use App\Models\User;
use App\Services\Reviews\NormalizedReview;
use App\Services\Reviews\Providers\GoogleReviewsProvider;
use App\Services\Reviews\Providers\YelpReviewsProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\Support\FakeReviewProvider;
use Tests\TestCase;

class ReviewSourceManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => User::ROLE_ADMIN]);
    }

    private function editor(): User
    {
        return User::factory()->create(['role' => User::ROLE_EDITOR]);
    }

    private function googleSource(): ReviewSource
    {
        $source = ReviewSource::query()->where('provider', ReviewSource::PROVIDER_GOOGLE)->firstOrFail();
        $source->update(['connected' => true, 'enabled' => true]);

        return $source;
    }

    public function test_editor_is_forbidden_from_the_sources_page(): void
    {
        $this->actingAs($this->editor())->get(route('admin.reviews.sources.index'))->assertForbidden();
    }

    public function test_admin_can_view_the_sources_page(): void
    {
        $this->actingAs($this->admin())->get(route('admin.reviews.sources.index'))
            ->assertOk()
            ->assertSee('Manual')
            ->assertSee('Google Business Profile')
            ->assertSee('Yelp')
            ->assertSee('Facebook');
    }

    public function test_sync_button_runs_synchronously_and_reports_counts(): void
    {
        $fake = new FakeReviewProvider;
        $fake->reviews = [new NormalizedReview(externalId: 'g-1', authorName: 'Jane Doe', rating: 5, content: 'Great job.')];
        $this->app->instance(GoogleReviewsProvider::class, $fake);

        $source = $this->googleSource();

        $response = $this->actingAs($this->admin())->post(route('admin.reviews.sources.sync', $source));

        $response->assertRedirect();
        $response->assertSessionHas('status', fn ($message) => str_contains($message, 'New: 1'));
        $this->assertSame(1, Review::query()->where('review_source_id', $source->id)->count());
    }

    public function test_failed_sync_is_reported_and_does_not_crash(): void
    {
        $fake = new FakeReviewProvider;
        $fake->throwSyncException = true;
        $this->app->instance(GoogleReviewsProvider::class, $fake);

        $source = $this->googleSource();

        $response = $this->actingAs($this->admin())->post(route('admin.reviews.sources.sync', $source));

        $response->assertRedirect();
        $this->assertSame(ReviewSource::STATUS_ERROR, $source->fresh()->sync_status);
        $this->assertNotNull($source->fresh()->sync_error);
    }

    public function test_duplicate_sync_does_not_create_duplicate_reviews(): void
    {
        $fake = new FakeReviewProvider;
        $fake->reviews = [new NormalizedReview(externalId: 'g-1', authorName: 'Jane Doe', rating: 5, content: 'Great job.')];
        $this->app->instance(GoogleReviewsProvider::class, $fake);

        $source = $this->googleSource();
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.reviews.sources.sync', $source));
        $this->actingAs($admin)->post(route('admin.reviews.sources.sync', $source));

        $this->assertSame(1, Review::query()->where('review_source_id', $source->id)->where('external_id', 'g-1')->count());
    }

    public function test_yelp_settings_are_saved_and_credentials_are_encrypted_at_rest(): void
    {
        // "Save & Test Connection" calls out to the real Yelp API — swap in
        // a fake so this test doesn't depend on the network.
        $this->app->instance(YelpReviewsProvider::class, new FakeReviewProvider);

        $yelp = ReviewSource::query()->where('provider', ReviewSource::PROVIDER_YELP)->firstOrFail();

        $this->actingAs($this->admin())->put(route('admin.reviews.sources.yelp.update', $yelp), [
            'api_key' => 'super-secret-yelp-key',
            'business_id' => 'my-business-chicago',
            'business_url' => 'https://www.yelp.com/biz/my-business-chicago',
        ])->assertRedirect();

        $yelp->refresh();
        $this->assertSame('my-business-chicago', $yelp->configValue('business_id'));
        $this->assertSame('super-secret-yelp-key', $yelp->credential('api_key'));

        $rawCredentials = DB::table('review_sources')->where('id', $yelp->id)->value('credentials');
        $this->assertStringNotContainsString('super-secret-yelp-key', (string) $rawCredentials);
    }

    public function test_credentials_are_hidden_from_array_and_json_serialization(): void
    {
        $yelp = ReviewSource::query()->where('provider', ReviewSource::PROVIDER_YELP)->firstOrFail();
        $yelp->update(['credentials' => ['api_key' => 'super-secret-yelp-key']]);

        $this->assertArrayNotHasKey('credentials', $yelp->fresh()->toArray());
        $this->assertStringNotContainsString('super-secret-yelp-key', $yelp->fresh()->toJson());
    }

    public function test_guest_cannot_reach_sources_page(): void
    {
        $this->get(route('admin.reviews.sources.index'))->assertRedirect(route('login'));
    }

    public function test_sync_all_queues_a_job_per_enabled_connected_source(): void
    {
        Queue::fake();
        $this->googleSource();

        $this->actingAs($this->admin())->post(route('admin.reviews.sources.sync-all'))->assertRedirect();

        Queue::assertPushed(SyncReviewSourceJob::class, function ($job) {
            return $job->source->provider === ReviewSource::PROVIDER_GOOGLE;
        });
    }

    public function test_cannot_sync_a_source_that_is_not_connected(): void
    {
        $google = ReviewSource::query()->where('provider', ReviewSource::PROVIDER_GOOGLE)->firstOrFail();

        $this->actingAs($this->admin())->post(route('admin.reviews.sources.sync', $google))->assertStatus(422);
    }

    public function test_disconnect_clears_credentials_and_marks_the_source_not_connected(): void
    {
        $google = $this->googleSource();
        $google->update(['credentials' => ['access_token' => 'abc'], 'config' => ['location_name' => 'locations/1']]);

        $this->actingAs($this->admin())->post(route('admin.reviews.sources.disconnect', $google))->assertRedirect();

        $google->refresh();
        $this->assertFalse($google->connected);
        $this->assertFalse($google->enabled);
        $this->assertNull($google->credential('access_token'));
    }
}
