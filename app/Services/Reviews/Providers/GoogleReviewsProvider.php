<?php

namespace App\Services\Reviews\Providers;

use App\Models\ReviewSource;
use App\Services\Reviews\ConnectionTestResult;
use App\Services\Reviews\NormalizedReview;
use App\Services\Reviews\ProviderSyncException;
use App\Services\Reviews\ReviewProviderInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Google Business Profile, via the official OAuth 2.0 + REST APIs — no
 * Maps scraping. Account/location discovery uses the current Business
 * Information API; reviews themselves are only exposed by the older
 * "Google My Business API v4", which Google keeps access-restricted (see
 * fetchReviews()) — this class calls the real endpoint correctly, but a
 * project must be granted access before it will return data.
 */
class GoogleReviewsProvider implements ReviewProviderInterface
{
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';

    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';

    private const ACCOUNTS_URL = 'https://mybusinessaccountmanagement.googleapis.com/v1/accounts';

    private const LOCATIONS_URL = 'https://mybusinessbusinessinformation.googleapis.com/v1';

    private const LEGACY_API_URL = 'https://mybusiness.googleapis.com/v4';

    private const SCOPE = 'https://www.googleapis.com/auth/business.manage';

    public function provider(): string
    {
        return ReviewSource::PROVIDER_GOOGLE;
    }

    public function getAuthorizationUrl(string $redirectUri, string $state): string
    {
        return self::AUTH_URL.'?'.http_build_query([
            'client_id' => config('services.google_business.client_id'),
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => self::SCOPE,
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ]);
    }

    public function handleCallback(ReviewSource $source, string $code, string $redirectUri): void
    {
        $response = Http::asForm()->post(self::TOKEN_URL, [
            'client_id' => config('services.google_business.client_id'),
            'client_secret' => config('services.google_business.client_secret'),
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri,
        ]);

        if ($response->failed()) {
            throw new ProviderSyncException('Google token exchange failed: '.$this->apiErrorMessage($response));
        }

        $tokens = $response->json();

        $source->forceFill([
            'credentials' => array_filter([
                'access_token' => $tokens['access_token'] ?? null,
                'refresh_token' => $tokens['refresh_token'] ?? $source->credential('refresh_token'),
                'expires_at' => now()->addSeconds((int) ($tokens['expires_in'] ?? 3600))->toIso8601String(),
            ]),
            'connected' => true,
            'enabled' => true,
        ])->save();
    }

    public function disconnect(ReviewSource $source): void
    {
        $source->forceFill([
            'credentials' => null,
            'connected' => false,
            'enabled' => false,
            'config' => null,
        ])->save();
    }

    /** @return list<array{name: string, accountName: string}> */
    public function listAccounts(ReviewSource $source): array
    {
        $response = $this->authedRequest($source)->get(self::ACCOUNTS_URL);

        if ($response->failed()) {
            throw new ProviderSyncException('Could not list Google Business accounts: '.$this->apiErrorMessage($response));
        }

        return $response->json('accounts', []);
    }

    /** @return list<array{name: string, title: string}> */
    public function listLocations(ReviewSource $source, string $accountName): array
    {
        $response = $this->authedRequest($source)->get(
            self::LOCATIONS_URL.'/'.$accountName.'/locations',
            ['readMask' => 'name,title']
        );

        if ($response->failed()) {
            throw new ProviderSyncException('Could not list Google Business locations: '.$this->apiErrorMessage($response));
        }

        return $response->json('locations', []);
    }

    public function selectLocation(ReviewSource $source, string $accountName, string $locationName, string $locationTitle): void
    {
        $source->forceFill([
            'config' => array_merge($source->config ?? [], [
                'account_name' => $accountName,
                'location_name' => $locationName,
                'location_title' => $locationTitle,
            ]),
        ])->save();
    }

    public function testConnection(ReviewSource $source): ConnectionTestResult
    {
        if (! $source->credential('access_token')) {
            return ConnectionTestResult::fail('Not connected — use "Connect with Google" first.');
        }

        if (! $source->configValue('location_name')) {
            return ConnectionTestResult::fail('Connected, but no location selected yet.');
        }

        try {
            $this->listAccounts($source);
        } catch (ProviderSyncException $e) {
            return ConnectionTestResult::fail($e->getMessage());
        }

        return ConnectionTestResult::ok('Connected to '.$source->configValue('location_title', 'Google Business Profile').'.');
    }

    /**
     * Reviews for a location are only exposed by the legacy "Google My
     * Business API v4" `accounts.locations.reviews.list` endpoint — the
     * newer Business Profile Performance/Business Information APIs that
     * replaced most of v4 do not cover reviews. Google keeps this endpoint
     * access-restricted: a project must apply for and be granted the
     * "Google My Business API" access level before it returns data, even
     * with valid OAuth tokens and the business.manage scope. This method
     * calls the real endpoint; until access is granted it fails with
     * Google's own permission error, surfaced as-is rather than faked.
     */
    public function fetchReviews(ReviewSource $source): array
    {
        $account = $source->configValue('account_name');
        $location = $source->configValue('location_name');

        if (! $account || ! $location) {
            throw new ProviderSyncException('No Google Business location selected.');
        }

        $response = $this->authedRequest($source)
            ->get(self::LEGACY_API_URL."/{$account}/{$location}/reviews");

        if ($response->status() === 403) {
            throw new ProviderSyncException(
                'Google denied access to the Reviews API for this project. Reviews require explicit '.
                'access approval from Google for the "Google My Business API" — request it in the Google '.
                'Cloud console for this OAuth client, this is not something this app can bypass.'
            );
        }

        if ($response->failed()) {
            throw new ProviderSyncException('Google review fetch failed: '.$this->apiErrorMessage($response));
        }

        return collect($response->json('reviews', []))
            ->map(fn (array $review) => new NormalizedReview(
                externalId: (string) Str::afterLast($review['reviewId'] ?? $review['name'], '/'),
                authorName: $review['reviewer']['displayName'] ?? 'Google user',
                authorAvatar: $review['reviewer']['profilePhotoUrl'] ?? null,
                rating: $this->starRatingToInt($review['starRating'] ?? null),
                content: $review['comment'] ?? '',
                reviewDate: isset($review['createTime']) ? new \DateTimeImmutable($review['createTime']) : null,
                reply: $review['reviewReply']['comment'] ?? null,
                replyDate: isset($review['reviewReply']['updateTime']) ? new \DateTimeImmutable($review['reviewReply']['updateTime']) : null,
                sourceUrl: null,
                metadata: ['raw_star_rating' => $review['starRating'] ?? null],
            ))
            ->all();
    }

    private function starRatingToInt(?string $rating): ?int
    {
        return match ($rating) {
            'ONE' => 1,
            'TWO' => 2,
            'THREE' => 3,
            'FOUR' => 4,
            'FIVE' => 5,
            default => null,
        };
    }

    private function authedRequest(ReviewSource $source)
    {
        $this->ensureFreshToken($source);

        return Http::withToken($source->credential('access_token'));
    }

    private function ensureFreshToken(ReviewSource $source): void
    {
        $expiresAt = $source->credential('expires_at');

        if (! $expiresAt || now()->lessThan(Carbon::parse($expiresAt)->subMinute())) {
            return;
        }

        $refreshToken = $source->credential('refresh_token');

        if (! $refreshToken) {
            throw new ProviderSyncException('Google access token expired and no refresh token is stored — reconnect the account.');
        }

        $response = Http::asForm()->post(self::TOKEN_URL, [
            'client_id' => config('services.google_business.client_id'),
            'client_secret' => config('services.google_business.client_secret'),
            'refresh_token' => $refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        if ($response->failed()) {
            throw new ProviderSyncException('Google token refresh failed: '.$this->apiErrorMessage($response));
        }

        $tokens = $response->json();

        $source->forceFill([
            'credentials' => array_merge($source->credentialsArray(), [
                'access_token' => $tokens['access_token'] ?? null,
                'expires_at' => now()->addSeconds((int) ($tokens['expires_in'] ?? 3600))->toIso8601String(),
            ]),
        ])->save();
    }

    private function apiErrorMessage(Response $response): string
    {
        return $response->json('error.message') ?? $response->json('error_description') ?? "HTTP {$response->status()}";
    }
}
