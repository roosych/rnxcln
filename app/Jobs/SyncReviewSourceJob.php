<?php

namespace App\Jobs;

use App\Models\ReviewSource;
use App\Services\Reviews\ReviewManager;
use App\Services\Reviews\SyncResult;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Used both queued (the 6-hourly scheduler, "Sync All") and via
 * dispatchSync() (the admin's single-source "Sync" button, which wants the
 * New/Updated/Skipped/Errors counts back immediately) — one code path
 * either way, ReviewManager does the actual work.
 */
class SyncReviewSourceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public ReviewSource $source) {}

    public function handle(ReviewManager $manager): SyncResult
    {
        return $manager->sync($this->source);
    }
}
