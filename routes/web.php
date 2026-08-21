<?php

use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

/*
| The marketing pages are static, so they go straight to a view. Only the two
| forms and the per-service page need a controller.
*/

Route::view('/', 'pages.home')->name('home');
Route::view('/about', 'pages.about')->name('about');
Route::view('/services', 'pages.services')->name('services');
Route::redirect('/services/carpet-upholstery', '/services/carpets-area-rugs', 301);
Route::get('/services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');
Route::view('/contact', 'pages.contact')->name('contact');

// Footer legal links — placeholder content until real copy is written in,
// see resources/views/pages/legal.blade.php and PageSeoRequest (robots is
// 'noindex' for these until then).
Route::view('/privacy-policy', 'pages.legal', ['title' => 'Privacy Policy'])->name('privacy-policy');
Route::view('/terms-and-conditions', 'pages.legal', ['title' => 'Terms and Conditions'])->name('terms-and-conditions');
Route::view('/cookie-policy', 'pages.legal', ['title' => 'Cookie Policy'])->name('cookie-policy');

// 5 submissions/minute/IP — enough for a genuine visitor (and a shared
// office IP) while blocking a bot flooding the form.
Route::post('/contact', [LeadController::class, 'store'])->name('contact.send')->middleware('throttle:5,1');

Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Admin login. No public registration — accounts are created with
// `php artisan admin:create-user`, so there is no open sign-up route.
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);

    Route::get('forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email')->middleware('throttle:5,1');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('logout', [LoginController::class, 'logout'])
    ->middleware('auth')->name('logout');

require __DIR__.'/admin.php';
