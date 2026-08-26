<?php

use App\Http\Controllers\TrialController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('trials')->as('trials.')->group(function () {
    Route::get('create', [TrialController::class, 'create'])->name('create');
    Route::post('/', [TrialController::class, 'store'])->name('store');
    Route::get('{trial}/edit', [TrialController::class, 'edit'])->whereNumber('trial')->name('edit');
    Route::put('{trial}', [TrialController::class, 'update'])->whereNumber('trial')->name('update');

    Route::get('{group}', [TrialController::class, 'index'])
        ->whereIn('group', ['approved', 'in-review', 'need-revision', 'rejected', 'waiting-approval', 'draft'])
        ->name('index');
});
