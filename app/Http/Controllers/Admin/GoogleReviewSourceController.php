<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReviewSource;
use App\Services\Reviews\Providers\GoogleReviewsProvider;
use App\Services\Reviews\ProviderSyncException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GoogleReviewSourceController extends Controller
{
    public function connect(GoogleReviewsProvider $provider): RedirectResponse
    {
        if (! config('services.google_business.client_id')) {
            return back()->with('error', 'Set GOOGLE_BUSINESS_CLIENT_ID / GOOGLE_BUSINESS_CLIENT_SECRET in .env first.');
        }

        $state = Str::random(40);
        session(['reviews.google.oauth_state' => $state]);

        return redirect()->away($provider->getAuthorizationUrl($this->redirectUri(), $state));
    }

    public function callback(Request $request, GoogleReviewsProvider $provider): RedirectResponse
    {
        if ($request->string('state') !== session('reviews.google.oauth_state')) {
            return $this->toSources()->with('error', 'Google sign-in state mismatch — please try connecting again.');
        }

        session()->forget('reviews.google.oauth_state');

        if ($request->filled('error')) {
            return $this->toSources()->with('error', 'Google sign-in was cancelled.');
        }

        $source = $this->source();

        try {
            $provider->handleCallback($source, $request->string('code')->value(), $this->redirectUri());
        } catch (ProviderSyncException $e) {
            return $this->toSources()->with('error', $e->getMessage());
        }

        return $this->toSources()->with('status', 'Connected to Google — now refresh locations and pick your business.');
    }

    public function refreshLocations(GoogleReviewsProvider $provider): RedirectResponse
    {
        $source = $this->source();

        try {
            $accounts = $provider->listAccounts($source);
        } catch (ProviderSyncException $e) {
            return $this->toSources()->with('error', $e->getMessage());
        }

        $locations = [];
        foreach ($accounts as $account) {
            foreach ($provider->listLocations($source, $account['name']) as $location) {
                $locations[] = [
                    'account_name' => $account['name'],
                    'location_name' => $location['name'],
                    'title' => $location['title'] ?? $location['name'],
                ];
            }
        }

        $source->update(['config' => array_merge($source->config ?? [], ['available_locations' => $locations])]);

        return $this->toSources()->with('status', count($locations).' location(s) found.');
    }

    public function selectLocation(Request $request, GoogleReviewsProvider $provider): RedirectResponse
    {
        // Packed as "account_name::location_name::title" by the <select> in
        // the sources view — simpler than round-tripping three hidden
        // inputs kept in sync via JS for what is always one atomic choice.
        [$account, $location, $title] = array_pad(
            explode('::', $request->validate(['location' => ['required', 'string']])['location'], 3),
            3,
            null
        );

        abort_if(! $account || ! $location || ! $title, 422);

        $provider->selectLocation($this->source(), $account, $location, $title);

        return $this->toSources()->with('status', "Now syncing reviews for {$title}.");
    }

    private function source(): ReviewSource
    {
        return ReviewSource::query()->where('provider', ReviewSource::PROVIDER_GOOGLE)->firstOrFail();
    }

    private function redirectUri(): string
    {
        return route('admin.reviews.sources.google.callback');
    }

    private function toSources(): RedirectResponse
    {
        return redirect()->route('admin.reviews.sources.index');
    }
}
