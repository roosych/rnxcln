<?php

namespace App\Services\Reviews;

/**
 * A review in a shape any provider can produce and the ReviewManager can
 * upsert into the `reviews` table without knowing where it came from.
 */
final readonly class NormalizedReview
{
    /** @param  array<string, mixed>  $metadata */
    public function __construct(
        public string $externalId,
        public string $authorName,
        public ?string $authorAvatar = null,
        public ?int $rating = null,
        public ?string $title = null,
        public string $content = '',
        public ?\DateTimeInterface $reviewDate = null,
        public ?string $reply = null,
        public ?\DateTimeInterface $replyDate = null,
        public ?string $sourceUrl = null,
        public array $metadata = [],
    ) {}
}
