<?php

namespace Tests\Feature\Admin;

use App\Models\Review;
use App\Models\ReviewSource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewManagementTest extends TestCase
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

    public function test_the_seed_migration_leaves_pre_existing_reviews_assigned_to_manual_and_unpublished(): void
    {
        // Simulates upgrading a database that already had reviews before
        // this system existed: delete what the migration already seeded,
        // insert a legacy-shaped row, then replay the migration's data step.
        ReviewSource::query()->delete();
        $legacy = Review::factory()->create(['review_source_id' => null, 'published' => true]);

        (require database_path('migrations/2026_08_21_100002_seed_review_sources_and_migrate_existing_reviews.php'))->up();

        $legacy->refresh();
        $this->assertFalse($legacy->published);
        $this->assertSame(ReviewSource::PROVIDER_MANUAL, $legacy->source->provider);
    }

    public function test_editor_can_create_a_manual_review(): void
    {
        $response = $this->actingAs($this->editor())->post(route('admin.reviews.store'), [
            'author_name' => 'Jane Doe',
            'location' => 'Chicago',
            'rating' => 5,
            'content' => 'Fantastic service.',
            'published' => '1',
        ]);

        $response->assertRedirect(route('admin.reviews.index'));

        $review = Review::sole();
        $this->assertSame('Jane Doe', $review->author_name);
        $this->assertTrue($review->published);
        $this->assertTrue($review->isManual());
        $this->assertSame(ReviewSource::PROVIDER_MANUAL, $review->source->provider);
    }

    public function test_editor_can_edit_a_manual_review(): void
    {
        $manual = ReviewSource::query()->where('provider', ReviewSource::PROVIDER_MANUAL)->firstOrFail();
        $review = Review::factory()->create(['review_source_id' => $manual->id, 'author_name' => 'Old Name']);

        $this->actingAs($this->editor())->put(route('admin.reviews.update', $review), [
            'author_name' => 'New Name',
            'rating' => 4,
            'content' => 'Updated text.',
            'published' => '1',
        ])->assertRedirect(route('admin.reviews.index'));

        $this->assertSame('New Name', $review->fresh()->author_name);
        $this->assertSame('Updated text.', $review->fresh()->content);
    }

    public function test_imported_review_content_cannot_be_edited_only_reply_and_moderation_flags(): void
    {
        $google = ReviewSource::query()->where('provider', ReviewSource::PROVIDER_GOOGLE)->firstOrFail();
        $review = Review::factory()->imported($google, 'g-123')->create([
            'author_name' => 'Real Customer',
            'content' => 'Original wording from Google.',
        ]);

        $this->actingAs($this->editor())->put(route('admin.reviews.update', $review), [
            'author_name' => 'Hacked Name',
            'content' => 'Hacked content.',
            'reply' => 'Thanks for the kind words!',
            'published' => '1',
            'verified' => '1',
        ])->assertRedirect(route('admin.reviews.index'));

        $review->refresh();
        $this->assertSame('Real Customer', $review->author_name);
        $this->assertSame('Original wording from Google.', $review->content);
        $this->assertSame('Thanks for the kind words!', $review->reply);
        $this->assertTrue($review->verified);
    }

    public function test_publish_toggle(): void
    {
        $review = Review::factory()->create(['published' => true]);

        $this->actingAs($this->editor())->post(route('admin.reviews.publish', $review))->assertRedirect();
        $this->assertFalse($review->fresh()->published);

        $this->actingAs($this->editor())->post(route('admin.reviews.publish', $review))->assertRedirect();
        $this->assertTrue($review->fresh()->published);
    }

    public function test_featured_toggle(): void
    {
        $review = Review::factory()->create(['featured' => false]);

        $this->actingAs($this->editor())->post(route('admin.reviews.feature', $review))->assertRedirect();
        $this->assertTrue($review->fresh()->featured);
    }

    public function test_index_filters_by_source(): void
    {
        $google = ReviewSource::query()->where('provider', ReviewSource::PROVIDER_GOOGLE)->firstOrFail();
        $manual = ReviewSource::query()->where('provider', ReviewSource::PROVIDER_MANUAL)->firstOrFail();

        $googleReview = Review::factory()->imported($google, 'g-1')->create(['author_name' => 'From Google']);
        $manualReview = Review::factory()->create(['review_source_id' => $manual->id, 'author_name' => 'From Manual']);

        $response = $this->actingAs($this->editor())->get(route('admin.reviews.index', ['source' => 'google']));

        $response->assertSee('From Google')->assertDontSee('From Manual');
        $this->assertNotNull($googleReview);
        $this->assertNotNull($manualReview);
    }

    public function test_guest_cannot_access_reviews_admin(): void
    {
        $this->get(route('admin.reviews.index'))->assertRedirect(route('login'));
    }
}
