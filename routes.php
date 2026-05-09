<?php

use Illuminate\Support\Facades\Route;
use MyAds\Plugins\MyStatistics\Src\Controllers\AdminStatisticsController;
use MyAds\Plugins\MyStatistics\Src\Controllers\TrackerController;

Route::middleware(['web'])->group(function () {
    // Client-side tracking endpoint (bypasses some ad blockers by using a custom path)
    Route::post('/beacon/track', [TrackerController::class, 'track'])->name('my_statistics.track');
});

Route::middleware(['web', 'auth', 'admin'])->group(function () {
    // Admin Dashboard
    Route::get('/admin/my-statistics', [AdminStatisticsController::class, 'index'])->name('admin.my_statistics.index');
});
