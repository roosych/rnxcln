<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewSource;
use App\Services\Reviews\Providers\FacebookReviewsProvider;
use App\Services\Reviews\ProviderSyncException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FacebookReviewSourceController extends Controller
{
    public function connect(FacebookReviewsProvider $provider): RedirectResponse
    {
        if (! config('services.facebook.client_id')) {
            return back()->with('error', 'Set FACEBOOK_CLIENT_ID / FACEBOOK_CLIENT_SECRET in .env first.');
        }

        $state = Str::random(40);
        session(['reviews.facebook.oauth_state' => $state]);

        return redirect()->away($provider->getAuthorizationUrl($this->redirectUri(), $state));
    }

    public function callback(Request $request, FacebookReviewsProvider $provider): RedirectResponse
    {
        if ($request->string('state') !== session('reviews.facebook.oauth_state')) {
            return $this->toSources()->with('error', 'Facebook sign-in state mismatch — please try connecting again.');
        }

        session()->forget('reviews.facebook.oauth_state');

        if ($request->filled('error')) {
            return $this->toSources()->with('error', 'Facebook sign-in was cancelled.');
        }

        $source = $this->source();

        try {
            $provider->handleCallback($source, $request->string('code')->value(), $this->redirectUri());
            $pages = $provider->listPages($source);
        } catch (ProviderSyncException $e) {
            return $this->toSources()->with('error', $e->getMessage());
        }

        $source->update(['config' => array_merge($source->config ?? [], ['available_pages' => $pages])]);

        return $this->toSources()->with('status', 'Connected to Facebook — now pick a Page.');
    }

    public function selectPage(Request $request, FacebookReviewsProvider $provider): RedirectResponse
    {
        // Packed as "id::name::access_token" — see GoogleReviewSourceController::selectLocation for why.
        [$id, $name, $token] = array_pad(
            explode('::', $request->validate(['page' => ['required', 'string']])['page'], 3),
            3,
            null
        );

        abort_if(! $id || ! $name || ! $token, 422);

        $provider->selectPage($this->source(), $id, $name, $token);

        return $this->toSources()->with('status', "Now syncing reviews for {$name}.");
    }

    private function source(): ReviewSource
    {
        return ReviewSource::query()->where('provider', ReviewSource::PROVIDER_FACEBOOK)->firstOrFail();
    }

    private function redirectUri(): string
    {
        return route('admin.reviews.sources.facebook.callback');
    }

    private function toSources(): RedirectResponse
    {
        return redirect()->route('admin.reviews.sources.index');
    }
}
