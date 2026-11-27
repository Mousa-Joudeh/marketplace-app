<?php

use App\Http\Controllers\Admin\CurrentAdminController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProviderController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('admin')->middleware(['auth:sanctum', 'can:access-admin'])->group(function () {
    Route::get('/me', CurrentAdminController::class)->name('admin.me');
    Route::get('/dashboard', DashboardController::class)->name('admin.dashboard');
    Route::get('/providers', [ProviderController::class, 'index'])->name('admin.providers.index');
});