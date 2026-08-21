<?php

use App\Models\ReviewSource;
use App\Models\Setting;
use App\Services\Reviews\ReviewManager;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Interval is configurable from Admin > Reviews > Sources
        // (settings.reviews.sync_interval_hours, default 6) rather than
        // fixed in code. `schedule:run` re-boots the app (and re-runs this
        // closure) on every tick of its own cron entry, so a changed
        // setting takes effect on the next tick without a deploy.
        $hours = max(1, (int) Setting::get('reviews', 'sync_interval_hours', 6));

        $schedule->call(fn () => app(ReviewManager::class)->syncAllEnabled())
            ->name('reviews:sync-all')
            ->cron("0 */{$hours} * * *")
            ->when(fn () => ReviewSource::query()->enabled()->connected()->exists());
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
