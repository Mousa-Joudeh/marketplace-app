<?php

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
});
