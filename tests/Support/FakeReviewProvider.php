<?php

namespace Tests\Support;

use App\Models\ReviewSource;
use App\Services\Reviews\ConnectionTestResult;
use App\Services\Reviews\NormalizedReview;
use App\Services\Reviews\ProviderSyncException;
use App\Services\Reviews\ProviderUnsupportedException;
use App\Services\Reviews\ReviewProviderInterface;

/**
 * Bound into the container in place of a real provider class (see
 * ReviewManager::$providers) so tests can exercise sync/testConnection
 * without calling a real external API.
 */
class FakeReviewProvider implements ReviewProviderInterface
{
    /** @var list<NormalizedReview> */
    public array $reviews = [];

    public bool $connectionOk = true;

    public string $connectionMessage = 'Connected.';

    public bool $throwSyncException = false;

    public bool $throwUnsupported = false;

    public function provider(): string
    {
        return ReviewSource::PROVIDER_CUSTOM;
    }

    public function testConnection(ReviewSource $source): ConnectionTestResult
    {
        return $this->connectionOk
            ? ConnectionTestResult::ok($this->connectionMessage)
            : ConnectionTestResult::fail($this->connectionMessage);
    }

    public function fetchReviews(ReviewSource $source): array
    {
        if ($this->throwSyncException) {
            throw new ProviderSyncException('Simulated API failure.');
        }

        if ($this->throwUnsupported) {
            throw new ProviderUnsupportedException('Simulated: requires API access.');
        }

        return $this->reviews;
    }
}
