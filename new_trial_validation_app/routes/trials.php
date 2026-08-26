<?php

use App\Http\Controllers\TrialController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('trials')->as('trials.')->group(function () {
    Route::get('{group}', [TrialController::class, 'index'])
        ->whereIn('group', ['approved', 'in-review', 'need-revision', 'rejected', 'waiting-approval'])
        ->name('index');
});
