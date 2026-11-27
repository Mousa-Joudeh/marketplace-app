<?php

use App\Http\Controllers\Admin\CurrentAdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('admin')->middleware(['auth:sanctum', 'can:access-admin'])->group(function () {
    Route::get('/me', CurrentAdminController::class)->name('admin.me');
});
