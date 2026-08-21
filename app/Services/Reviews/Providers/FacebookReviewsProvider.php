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
 * Facebook Page ratings/recommendations, via the official Meta Graph API —
 * no scraping. Reading a Page's ratings requires the `pages_read_engagement`
 * permission with the Page's own access token, and Meta gates that
 * permission behind App Review for any app that isn't in development mode
 * on a page the developer administers — a real platform restriction this
 * class surfaces rather than works around.
 */
class FacebookReviewsProvider implements ReviewProviderInterface
{
    private const GRAPH_VERSION = 'v21.0';

    private const OAUTH_DIALOG_URL = 'https://www.facebook.com/'.self::GRAPH_VERSION.'/dialog/oauth';

    private const GRAPH_URL = 'https://graph.facebook.com/'.self::GRAPH_VERSION;

    private const SCOPE = 'pages_show_list,pages_read_engagement,pages_read_user_content';

    public function provider(): string
    {
        return ReviewSource::PROVIDER_FACEBOOK;
    }

    public function getAuthorizationUrl(string $redirectUri, string $state): string
    {
        return self::OAUTH_DIALOG_URL.'?'.http_build_query([
            'client_id' => config('services.facebook.client_id'),
            'redirect_uri' => $redirectUri,
            'scope' => self::SCOPE,
            'state' => $state,
            'response_type' => 'code',
        ]);
    }

    public function handleCallback(ReviewSource $source, string $code, string $redirectUri): void
    {
        $response = Http::get(self::GRAPH_URL.'/oauth/access_token', [
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'redirect_uri' => $redirectUri,
            'code' => $code,
        ]);

        if ($response->failed()) {
            throw new ProviderSyncException('Facebook token exchange failed: '.$this->apiErrorMessage($response));
        }

        // Exchange for a long-lived user token so the connection doesn't
        // expire after the ~1-2 hour short-lived token Facebook returns
        // above.
        $longLived = Http::get(self::GRAPH_URL.'/oauth/access_token', [
            'grant_type' => 'fb_exchange_token',
            'client_id' => config('services.facebook.client_id'),
            'client_secret' => config('services.facebook.client_secret'),
            'fb_exchange_token' => $response->json('access_token'),
        ]);

        $source->forceFill([
            'credentials' => [
                'user_access_token' => $longLived->successful()
                    ? $longLived->json('access_token')
                    : $response->json('access_token'),
            ],
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

    /** @return list<array{id: string, name: string, access_token: string}> */
    public function listPages(ReviewSource $source): array
    {
        $response = Http::get(self::GRAPH_URL.'/me/accounts', [
            'access_token' => $source->credential('user_access_token'),
        ]);

        if ($response->failed()) {
            throw new ProviderSyncException('Could not list Facebook Pages: '.$this->apiErrorMessage($response));
        }

        return $response->json('data', []);
    }

    public function selectPage(ReviewSource $source, string $pageId, string $pageName, string $pageAccessToken): void
    {
        $source->forceFill([
            // The Page access token (not the user token) is what's needed
            // to read the Page's own ratings edge, so it's stored alongside
            // the page identity rather than re-derived from listPages()
            // on every sync.
            'credentials' => array_merge($source->credentialsArray(), [
                'page_access_token' => $pageAccessToken,
            ]),
            'config' => array_merge($source->config ?? [], [
                'page_id' => $pageId,
                'page_name' => $pageName,
            ]),
        ])->save();
    }

    public function testConnection(ReviewSource $source): ConnectionTestResult
    {
        if (! $source->credential('user_access_token')) {
            return ConnectionTestResult::fail('Not connected — use "Connect Facebook" first.');
        }

        if (! $source->configValue('page_id')) {
            return ConnectionTestResult::fail('Connected, but no Page selected yet.');
        }

        $response = Http::get(self::GRAPH_URL.'/'.$source->configValue('page_id'), [
            'fields' => 'name',
            'access_token' => $source->credential('page_access_token'),
        ]);

        if ($response->failed()) {
            return ConnectionTestResult::fail($this->apiErrorMessage($response));
        }

        return ConnectionTestResult::ok('Connected to '.$source->configValue('page_name', 'Facebook Page').'.');
    }

    /**
     * @throws ProviderUnsupportedException when Meta denies the `ratings`
     *                                      read for this app/Page — in practice this is the default
     *                                      state for any app that hasn't been through Meta's App Review
     *                                      for `pages_read_engagement`/`pages_read_user_content`, or
     *                                      whose Business isn't verified. That approval process is
     *                                      entirely on Meta's side and cannot be bypassed here.
     */
    public function fetchReviews(ReviewSource $source): array
    {
        $pageId = $source->configValue('page_id');
        $pageToken = $source->credential('page_access_token');

        if (! $pageId || ! $pageToken) {
            throw new ProviderSyncException('No Facebook Page selected.');
        }

        $response = Http::get(self::GRAPH_URL."/{$pageId}/ratings", [
            'fields' => 'reviewer,rating,review_text,created_time,recommendation_type',
            'access_token' => $pageToken,
        ]);

        if ($response->status() === 403 || $response->json('error.code') === 10) {
            throw new ProviderUnsupportedException(
                'Meta denied access to this Page\'s ratings. Requires API Access: reading Page reviews needs '.
                '`pages_read_engagement` approved through Meta App Review (and a verified Business) for this '.
                'app — this cannot be worked around.'
            );
        }

        if ($response->failed()) {
            throw new ProviderSyncException('Facebook review fetch failed: '.$this->apiErrorMessage($response));
        }

        return collect($response->json('data', []))
            ->filter(fn (array $rating) => isset($rating['review_text']) || isset($rating['rating']))
            ->map(fn (array $rating) => new NormalizedReview(
                externalId: $rating['open_graph_story']['id'] ?? md5($rating['reviewer']['id'].$rating['created_time']),
                authorName: $rating['reviewer']['name'] ?? 'Facebook user',
                rating: $rating['rating'] ?? ($rating['recommendation_type'] === 'positive' ? 5 : 1),
                content: $rating['review_text'] ?? '',
                reviewDate: isset($rating['created_time']) ? new \DateTimeImmutable($rating['created_time']) : null,
            ))
            ->values()
            ->all();
    }

    private function apiErrorMessage(Response $response): string
    {
        return $response->json('error.message') ?? "HTTP {$response->status()}";
    }
}
