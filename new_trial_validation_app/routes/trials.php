<?php

use App\Http\Controllers\TrialController;
use App\Http\Controllers\TrialValidationController;
use App\Http\Controllers\TrialWeighingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('trials')->as('trials.')->group(function () {
    Route::get('create', [TrialController::class, 'create'])->name('create');
    Route::post('/', [TrialController::class, 'store'])->name('store');
    Route::get('{trial}/edit', [TrialController::class, 'edit'])->whereNumber('trial')->name('edit');
    Route::put('{trial}', [TrialController::class, 'update'])->whereNumber('trial')->name('update');

    Route::get('{trial}/validation', [TrialValidationController::class, 'edit'])->whereNumber('trial')->name('validation.edit');
    Route::put('{trial}/validation', [TrialValidationController::class, 'update'])->whereNumber('trial')->name('validation.update');

    Route::get('{trial}/weighing/{section}', [TrialWeighingController::class, 'edit'])
        ->whereNumber('trial')->whereIn('section', ['Packaging', 'Filling'])->name('weighing.edit');
    Route::put('{trial}/weighing/{section}', [TrialWeighingController::class, 'update'])
        ->whereNumber('trial')->whereIn('section', ['Packaging', 'Filling'])->name('weighing.update');

    Route::get('{group}', [TrialController::class, 'index'])
        ->whereIn('group', ['approved', 'in-review', 'need-revision', 'rejected', 'waiting-approval', 'draft'])
        ->name('index');
});
