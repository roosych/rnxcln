<?php

namespace App\Services\Reviews\Providers;

use App\Models\ReviewSource;
use App\Services\Reviews\ConnectionTestResult;
use App\Services\Reviews\ReviewProviderInterface;

/**
 * Manual reviews are entered by an admin through the Reviews CRUD, not
 * fetched from anywhere — this provider only exists so Manual behaves like
 * any other source in the sources list (always "connected", nothing to
 * sync).
 */
class ManualReviewProvider implements ReviewProviderInterface
{
    public function provider(): string
    {
        return ReviewSource::PROVIDER_MANUAL;
    }

    public function testConnection(ReviewSource $source): ConnectionTestResult
    {
        return ConnectionTestResult::ok('Manual reviews are added directly, no connection needed.');
    }

    public function fetchReviews(ReviewSource $source): array
    {
        return [];
    }
}
