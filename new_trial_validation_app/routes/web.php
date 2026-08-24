<?php

use App\Http\Controllers\SsoController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::get('sso/exchange', [SsoController::class, 'exchange'])->name('sso.exchange');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
    Route::get('sso/to-old', [SsoController::class, 'toOld'])->name('sso.to-old');
});

require __DIR__.'/settings.php';
