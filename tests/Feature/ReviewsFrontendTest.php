<?php

namespace Tests\Feature;

use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ReviewsFrontendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The rest of the homepage (stats counters, footer socials, etc.)
        // reads site settings normally populated by the one-time
        // `content:import-config` command, not by RefreshDatabase — run it
        // so '/' renders, isolating these tests to the Reviews behavior
        // they actually check. It also imports 6 demo reviews, but those
        // land unpublished (see ImportConfigContent::importReviews), so
        // they don't affect the published-only assertions below.
        Artisan::call('content:import-config');
    }

    public function test_homepage_only_shows_published_reviews(): void
    {
        Review::factory()->create(['author_name' => 'Visible Customer', 'published' => true]);
        Review::factory()->create(['author_name' => 'Hidden Customer', 'published' => false]);

        $this->get('/')->assertOk()->assertSee('Visible Customer')->assertDontSee('Hidden Customer');
    }

    public function test_schema_aggregate_rating_only_counts_published_reviews(): void
    {
        Review::factory()->create(['published' => true]);
        Review::factory()->create(['published' => false]);
        Review::factory()->create(['published' => false]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('"reviewCount":1', false);
    }
}
