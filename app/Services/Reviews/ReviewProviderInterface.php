<?php

namespace App\Services\Reviews;

use App\Models\ReviewSource;

/**
 * One implementation per review source. The ReviewManager is the only
 * caller — it never talks to an external API directly, and adding a new
 * source means adding a new class here, not touching the manager, the
 * controller, or the `reviews` table.
 */
interface ReviewProviderInterface
{
    /** Matches ReviewSource::PROVIDER_* */
    public function provider(): string;

    /**
     * Verifies the stored config/credentials actually work against the
     * external API. Returns a human-readable status message either way.
     */
    public function testConnection(ReviewSource $source): ConnectionTestResult;

    /**
     * @return list<NormalizedReview>
     *
     * @throws ProviderSyncException when the API call itself fails (auth
     *                               expired, network error, rate limit, etc).
     * @throws ProviderUnsupportedException when the provider cannot fetch
     *                                      reviews at all under the current API access level — this is
     *                                      not an error, it's a known platform limitation to surface
     *                                      as-is rather than work around.
     */
    public function fetchReviews(ReviewSource $source): array;
}
