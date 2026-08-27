<?php

use App\Http\Controllers\TrialAttachmentController;
use App\Http\Controllers\TrialController;
use App\Http\Controllers\TrialReviewController;
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

    Route::get('{trial}/attachments', [TrialAttachmentController::class, 'edit'])->whereNumber('trial')->name('attachments.edit');
    Route::post('{trial}/attachments', [TrialAttachmentController::class, 'store'])->whereNumber('trial')->name('attachments.store');
    Route::delete('{trial}/attachments/{attachment}', [TrialAttachmentController::class, 'destroy'])
        ->whereNumber('trial')->whereNumber('attachment')->name('attachments.destroy');
    Route::get('{trial}/attachments/{attachment}/file', [TrialAttachmentController::class, 'show'])
        ->whereNumber('trial')->whereNumber('attachment')->name('attachments.show');

    Route::get('{trial}/review', [TrialReviewController::class, 'edit'])->whereNumber('trial')->name('review.edit');
    Route::post('{trial}/review', [TrialReviewController::class, 'store'])->whereNumber('trial')->name('review.store');

    Route::get('{group}', [TrialController::class, 'index'])
        ->whereIn('group', ['approved', 'in-review', 'need-revision', 'rejected', 'waiting-approval', 'draft'])
        ->name('index');
});
