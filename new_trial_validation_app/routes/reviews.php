<?php

use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::put('reviews/{review}', [ReviewController::class, 'update'])->whereNumber('review')->name('reviews.update');
});
