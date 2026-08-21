<?php

namespace Database\Factories;

use App\Models\ReviewSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReviewSource>
 */
class ReviewSourceFactory extends Factory
{
    protected $model = ReviewSource::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'provider' => ReviewSource::PROVIDER_CUSTOM,
            'type' => ReviewSource::TYPE_API_KEY,
            'enabled' => true,
            'connected' => true,
        ];
    }

    /** Fallback only — ReviewFactory's default reuses the migration-seeded Manual row instead of calling this. */
    public function manual(): static
    {
        return $this->state(fn () => [
            'name' => 'Manual',
            'provider' => ReviewSource::PROVIDER_MANUAL,
            'type' => ReviewSource::TYPE_MANUAL,
        ]);
    }
}
