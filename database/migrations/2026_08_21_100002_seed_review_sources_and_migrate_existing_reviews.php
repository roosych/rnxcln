<?php

use App\Models\Review;
use App\Models\ReviewSource;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Seeds the four built-in sources and assigns every review that
     * predates this system (the demo rows imported by
     * content:import-config) to Manual, unpublished — they're sample
     * content, not real reviews, so they must not show as live on
     * production once this ships.
     */
    public function up(): void
    {
        $manual = ReviewSource::create([
            'name' => 'Manual',
            'provider' => ReviewSource::PROVIDER_MANUAL,
            'type' => ReviewSource::TYPE_MANUAL,
            'enabled' => true,
            'connected' => true,
        ]);

        ReviewSource::create([
            'name' => 'Google Business Profile',
            'provider' => ReviewSource::PROVIDER_GOOGLE,
            'type' => ReviewSource::TYPE_OAUTH,
            'enabled' => false,
            'connected' => false,
        ]);

        ReviewSource::create([
            'name' => 'Yelp',
            'provider' => ReviewSource::PROVIDER_YELP,
            'type' => ReviewSource::TYPE_API_KEY,
            'enabled' => false,
            'connected' => false,
        ]);

        ReviewSource::create([
            'name' => 'Facebook',
            'provider' => ReviewSource::PROVIDER_FACEBOOK,
            'type' => ReviewSource::TYPE_OAUTH,
            'enabled' => false,
            'connected' => false,
        ]);

        Review::query()->whereNull('review_source_id')->update([
            'review_source_id' => $manual->id,
            'published' => false,
        ]);
    }

    public function down(): void
    {
        ReviewSource::query()->whereIn('provider', [
            ReviewSource::PROVIDER_MANUAL,
            ReviewSource::PROVIDER_GOOGLE,
            ReviewSource::PROVIDER_YELP,
            ReviewSource::PROVIDER_FACEBOOK,
        ])->delete();
    }
};
