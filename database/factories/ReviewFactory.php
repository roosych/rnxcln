<?php

namespace Database\Factories;

use App\Models\Review;
use App\Models\ReviewSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            // The seed migration already creates the one Manual row, so
            // reuse it instead of ReviewSource::factory()->manual() —
            // that would insert a second row and violate the unique
            // constraint on `provider`.
            'review_source_id' => fn () => ReviewSource::query()->where('provider', ReviewSource::PROVIDER_MANUAL)->value('id')
                ?? ReviewSource::factory()->manual()->create()->id,
            'author_name' => fake()->name(),
            'location' => fake()->city(),
            'rating' => fake()->numberBetween(1, 5),
            'content' => fake()->paragraph(),
            'review_date' => fake()->date(),
            'published' => true,
            'featured' => false,
            'verified' => false,
        ];
    }

    public function unpublished(): static
    {
        return $this->state(fn () => ['published' => false]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['featured' => true]);
    }

    public function imported(ReviewSource $source, ?string $externalId = null): static
    {
        return $this->state(fn () => [
            'review_source_id' => $source->id,
            'external_id' => $externalId ?? fake()->unique()->uuid(),
        ]);
    }
}
