<?php

use App\Http\Controllers\Admin\AdminInsightsController;
use App\Http\Controllers\Admin\AiOperationsController;
use App\Http\Controllers\Admin\AnalyticsController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CurrentAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProviderController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WorkQueueController;
use App\Http\Middleware\EnsureAccountIsActive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum', EnsureAccountIsActive::class]);

Route::prefix('admin')->middleware(['auth:sanctum', EnsureAccountIsActive::class, 'can:access-admin'])->group(function () {
    Route::get('/me', CurrentAdminController::class)->name('admin.me');
    Route::get('/dashboard', DashboardController::class)->name('admin.dashboard');
    Route::get('/providers', [ProviderController::class, 'index'])->name('admin.providers.index');
    Route::get('/providers/{provider}', [ProviderController::class, 'show'])
        ->whereNumber('provider')
        ->name('admin.providers.show');
    Route::patch('/providers/{provider}/approval', [ProviderController::class, 'review'])
        ->whereNumber('provider')
        ->name('admin.providers.review');
    Route::get('/categories', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/categories/{category}', [CategoryController::class, 'show'])
        ->whereNumber('category')
        ->name('admin.categories.show');
    Route::post('/categories', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::patch('/categories/{category}', [CategoryController::class, 'update'])
        ->whereNumber('category')
        ->name('admin.categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
        ->whereNumber('category')
        ->name('admin.categories.destroy');
    Route::get('/catalog/{type}', [CatalogController::class, 'index'])
        ->whereIn('type', array_keys(CatalogController::MODELS))
        ->name('admin.catalog.index');
    Route::get('/catalog/{type}/{item}', [CatalogController::class, 'show'])
        ->whereIn('type', array_keys(CatalogController::MODELS))
        ->whereNumber('item')
        ->name('admin.catalog.show');
    Route::patch('/catalog/{type}/{item}/status', [CatalogController::class, 'updateStatus'])
        ->whereIn('type', array_keys(CatalogController::MODELS))
        ->whereNumber('item')
        ->name('admin.catalog.update-status');
    Route::get('/users', [UserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])
        ->whereNumber('user')
        ->name('admin.users.show');
    Route::patch('/users/{user}/status', [UserController::class, 'updateStatus'])
        ->whereNumber('user')
        ->name('admin.users.update-status');
    Route::get('/bookings', [BookingController::class, 'index'])->name('admin.bookings.index');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])
        ->whereNumber('booking')
        ->name('admin.bookings.show');
    Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/payments/{payment}', [PaymentController::class, 'show'])
        ->whereNumber('payment')
        ->name('admin.payments.show');
    Route::get('/reviews', [ReviewController::class, 'index'])->name('admin.reviews.index');
    Route::get('/reviews/{review}', [ReviewController::class, 'show'])
        ->whereNumber('review')
        ->name('admin.reviews.show');
    Route::patch('/reviews/{review}/status', [ReviewController::class, 'updateStatus'])
        ->whereNumber('review')
        ->name('admin.reviews.update-status');
    Route::get('/work-queue', WorkQueueController::class)->name('admin.work-queue');
    Route::get('/analytics', AnalyticsController::class)->name('admin.analytics');
    Route::post('/ai/insights', AdminInsightsController::class)
        ->middleware('throttle:5,1')
        ->name('admin.ai.insights');
    Route::get('/ai/operations', AiOperationsController::class)->name('admin.ai.operations');
});
