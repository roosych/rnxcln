<?php

use App\Http\Controllers\Admin\FacebookReviewSourceController;
use App\Http\Controllers\Admin\FaqItemController;
use App\Http\Controllers\Admin\GoogleReviewSourceController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\Admin\ProcessStepController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\ReviewSourceController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ServiceAreaController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

/*
| Admin panel routes. Everything here requires an authenticated user;
| Settings/SEO/Users additionally require the `manage-settings` /
| `manage-users` gates, so editors can't reach them even via a direct URL.
*/

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::view('/', 'admin.dashboard')->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('leads/export', [AdminLeadController::class, 'export'])->name('leads.export');
    Route::resource('leads', AdminLeadController::class)->only(['index', 'update']);

    Route::post('services/reorder', [ServiceController::class, 'reorder'])->name('services.reorder');
    Route::put('services/folders', [ServiceController::class, 'updateFolderNames'])->name('services.folders.update');
    Route::resource('services', ServiceController::class)->except(['show']);

    // 'create' is dropped from all four resources below: adding a new one
    // happens from a modal on the index page instead of its own page, so
    // nothing links to a dedicated "create" screen any more — 'store' is
    // still there, the modal's form posts straight to it.
    Route::post('faq/reorder', [FaqItemController::class, 'reorder'])->name('faq.reorder');
    Route::resource('faq', FaqItemController::class)->except(['show', 'create'])->parameters(['faq' => 'faqItem']);

    Route::post('process-steps/reorder/{group}', [ProcessStepController::class, 'reorder'])->name('process-steps.reorder');
    Route::resource('process-steps', ProcessStepController::class)->except(['show', 'create'])->parameters(['process-steps' => 'processStep']);

    Route::post('reviews/{review}/publish', [ReviewController::class, 'publish'])->name('reviews.publish');
    Route::post('reviews/{review}/feature', [ReviewController::class, 'feature'])->name('reviews.feature');
    Route::resource('reviews', ReviewController::class)->except(['show', 'create']);

    Route::resource('service-areas', ServiceAreaController::class)->except(['show', 'create'])->parameters(['service-areas' => 'serviceArea']);

    Route::middleware('can:manage-settings')->group(function () {
        Route::get('settings/{group?}', [SettingsController::class, 'edit'])->name('settings.index');
        Route::put('settings/{group}', [SettingsController::class, 'update'])->name('settings.update');

        Route::get('seo', [SeoController::class, 'index'])->name('seo.index');
        Route::get('seo/{pageSeo}/edit', [SeoController::class, 'edit'])->name('seo.edit');
        Route::put('seo/{pageSeo}', [SeoController::class, 'update'])->name('seo.update');
        Route::put('seo-analytics', [SeoController::class, 'updateAnalytics'])->name('seo.analytics.update');

        // Credentials/tokens live behind this gate — admin only, unlike the
        // Reviews CRUD above which any authenticated editor can use.
        Route::prefix('reviews/sources')->name('reviews.sources.')->group(function () {
            Route::get('/', [ReviewSourceController::class, 'index'])->name('index');
            Route::put('interval', [ReviewSourceController::class, 'updateInterval'])->name('interval');
            Route::post('sync-all', [ReviewSourceController::class, 'syncAll'])->name('sync-all');

            Route::get('google/connect', [GoogleReviewSourceController::class, 'connect'])->name('google.connect');
            Route::get('google/callback', [GoogleReviewSourceController::class, 'callback'])->name('google.callback');
            Route::post('google/locations/refresh', [GoogleReviewSourceController::class, 'refreshLocations'])->name('google.locations.refresh');
            Route::post('google/location', [GoogleReviewSourceController::class, 'selectLocation'])->name('google.location');

            Route::get('facebook/connect', [FacebookReviewSourceController::class, 'connect'])->name('facebook.connect');
            Route::get('facebook/callback', [FacebookReviewSourceController::class, 'callback'])->name('facebook.callback');
            Route::post('facebook/page', [FacebookReviewSourceController::class, 'selectPage'])->name('facebook.page');

            Route::put('{reviewSource}/yelp', [ReviewSourceController::class, 'updateYelp'])->name('yelp.update');
            Route::post('{reviewSource}/test', [ReviewSourceController::class, 'test'])->name('test');
            Route::post('{reviewSource}/sync', [ReviewSourceController::class, 'sync'])->name('sync');
            Route::post('{reviewSource}/disconnect', [ReviewSourceController::class, 'disconnect'])->name('disconnect');
        });
    });

    Route::middleware('can:manage-users')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });
});
