<?php

namespace App\Services\Reviews\Providers;

use App\Models\ReviewSource;
use App\Services\Reviews\ConnectionTestResult;
use App\Services\Reviews\NormalizedReview;
use App\Services\Reviews\ProviderSyncException;
use App\Services\Reviews\ProviderUnsupportedException;
use App\Services\Reviews\ReviewProviderInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Yelp Fusion API — no scraping. Fusion's public /reviews endpoint is a
 * genuine, real limitation: it returns at most 3 review excerpts per
 * business and does not paginate, by Yelp's own design (full review sync
 * needs Yelp's separate, invite-only Reviews API for approved partners).
 * This provider fetches exactly what the public key is allowed to fetch
 * and is honest about the rest via ProviderUnsupportedException.
 */
class YelpReviewsProvider implements ReviewProviderInterface
{
    private const API_URL = 'https://api.yelp.com/v3';

    public function provider(): string
    {
        return ReviewSource::PROVIDER_YELP;
    }

    public function testConnection(ReviewSource $source): ConnectionTestResult
    {
        $apiKey = $source->credential('api_key');
        $businessId = $source->configValue('business_id');

        if (! $apiKey || ! $businessId) {
            return ConnectionTestResult::fail('API key and Business ID are both required.');
        }

        $response = Http::withToken($apiKey)->get(self::API_URL."/businesses/{$businessId}");

        if ($response->status() === 404) {
            return ConnectionTestResult::fail("No Yelp business found for ID '{$businessId}'.");
        }

        if ($response->failed()) {
            return ConnectionTestResult::fail('Yelp rejected the request: '.$this->apiErrorMessage($response));
        }

        return ConnectionTestResult::ok('Connected to '.$response->json('name', 'Yelp business').'.');
    }

    /**
     * @throws ProviderUnsupportedException when this key has no review
     *                                      access at all (a 403 from Yelp itself). When it succeeds, the
     *                                      result is capped at 3 reviews by Yelp's own API — that's not
     *                                      an error, just this endpoint's documented limit.
     */
    public function fetchReviews(ReviewSource $source): array
    {
        $apiKey = $source->credential('api_key');
        $businessId = $source->configValue('business_id');

        if (! $apiKey || ! $businessId) {
            throw new ProviderSyncException('Yelp is not configured — save an API key and Business ID first.');
        }

        $response = Http::withToken($apiKey)->get(self::API_URL."/businesses/{$businessId}/reviews", [
            'sort_by' => 'newest',
        ]);

        if ($response->status() === 403) {
            throw new ProviderUnsupportedException(
                'This Yelp API key does not have review access. Requires API Access: full review sync is only '.
                'available through Yelp\'s separate, partner-only Reviews API — this cannot be worked around.'
            );
        }

        if ($response->failed()) {
            throw new ProviderSyncException('Yelp review fetch failed: '.$this->apiErrorMessage($response));
        }

        return collect($response->json('reviews', []))
            ->map(fn (array $review) => new NormalizedReview(
                externalId: $review['id'],
                authorName: $review['user']['name'] ?? 'Yelp user',
                authorAvatar: $review['user']['image_url'] ?? null,
                rating: $review['rating'] ?? null,
                content: $review['text'] ?? '',
                reviewDate: isset($review['time_created']) ? new \DateTimeImmutable($review['time_created']) : null,
                sourceUrl: $review['url'] ?? null,
                metadata: ['note' => 'Yelp Fusion API caps this endpoint at 3 reviews per business.'],
            ))
            ->all();
    }

    private function apiErrorMessage(Response $response): string
    {
        return $response->json('error.description') ?? "HTTP {$response->status()}";
    }
}
