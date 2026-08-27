<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SsoController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::get('sso/exchange', [SsoController::class, 'exchange'])->name('sso.exchange');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('sso/to-old', [SsoController::class, 'toOld'])->name('sso.to-old');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
require __DIR__.'/trials.php';
require __DIR__.'/reviews.php';
