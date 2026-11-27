<?php

use App\Http\Controllers\Admin\CatalogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CurrentAdminController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProviderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('admin')->middleware(['auth:sanctum', 'can:access-admin'])->group(function () {
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
});
