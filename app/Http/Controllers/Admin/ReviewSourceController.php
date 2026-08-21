<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\YelpSourceRequest;
use App\Jobs\SyncReviewSourceJob;
use App\Models\ReviewSource;
use App\Models\Setting;
use App\Services\Reviews\ReviewManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewSourceController extends Controller
{
    public function index(): View
    {
        $sources = ReviewSource::query()->orderByRaw("CASE provider WHEN 'manual' THEN 0 WHEN 'google' THEN 1 WHEN 'yelp' THEN 2 WHEN 'facebook' THEN 3 ELSE 4 END")->get();
        $syncIntervalHours = (int) Setting::get('reviews', 'sync_interval_hours', 6);

        return view('admin.reviews.sources.index', compact('sources', 'syncIntervalHours'));
    }

    public function updateYelp(YelpSourceRequest $request, ReviewSource $reviewSource): RedirectResponse
    {
        abort_unless($reviewSource->provider === ReviewSource::PROVIDER_YELP, 404);

        $reviewSource->update([
            'credentials' => ['api_key' => $request->validated('api_key')],
            'config' => [
                'business_id' => $request->validated('business_id'),
                'business_url' => $request->validated('business_url'),
            ],
        ]);

        $result = app(ReviewManager::class)->testConnection($reviewSource);

        $reviewSource->update([
            'connected' => $result->ok,
            'enabled' => $result->ok,
        ]);

        return back()->with($result->ok ? 'status' : 'error', $result->message);
    }

    public function test(ReviewSource $reviewSource): RedirectResponse
    {
        $result = app(ReviewManager::class)->testConnection($reviewSource);

        return back()->with($result->ok ? 'status' : 'error', $result->message);
    }

    public function sync(ReviewSource $reviewSource): RedirectResponse
    {
        abort_unless($reviewSource->enabled && $reviewSource->connected, 422, 'Source is not connected.');

        // Not SyncReviewSourceJob::dispatchSync(): a ShouldQueue job's
        // dispatchSync() still round-trips through the queue (the "sync"
        // connection) and returns the pushed job id, not the handler's
        // return value — calling ReviewManager directly is what actually
        // runs inline and gets the SyncResult back for the flash message.
        $result = app(ReviewManager::class)->sync($reviewSource);

        if ($result->unsupported) {
            return back()->with('error', $result->errorMessages[0] ?? 'This source does not support review sync under the current API access.');
        }

        return back()->with('status', $result->summary().($result->hasErrors() ? ' — '.($result->errorMessages[0] ?? '') : ''));
    }

    public function syncAll(): RedirectResponse
    {
        $sources = ReviewSource::query()->enabled()->connected()->get();

        foreach ($sources as $source) {
            SyncReviewSourceJob::dispatch($source);
        }

        return back()->with('status', "Sync queued for {$sources->count()} source(s).");
    }

    public function disconnect(ReviewSource $reviewSource): RedirectResponse
    {
        if ($reviewSource->isManual()) {
            abort(422, 'Manual cannot be disconnected.');
        }

        $reviewSource->update([
            'credentials' => null,
            'config' => null,
            'connected' => false,
            'enabled' => false,
            'sync_status' => null,
            'sync_error' => null,
        ]);

        return back()->with('status', "{$reviewSource->name} disconnected.");
    }

    public function updateInterval(Request $request): RedirectResponse
    {
        $hours = $request->validate(['sync_interval_hours' => ['required', 'integer', 'min:1', 'max:168']])['sync_interval_hours'];

        Setting::put('reviews', 'sync_interval_hours', $hours);

        return back()->with('status', 'Sync interval updated.');
    }
}
