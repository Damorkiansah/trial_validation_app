<?php

use App\Http\Controllers\ApprovalController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('approvals', [ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('trials/{trial}/approval', [ApprovalController::class, 'update'])->whereNumber('trial')->name('approvals.update');
});
