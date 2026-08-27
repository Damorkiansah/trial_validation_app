<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('reports')->as('reports.')->group(function () {
    Route::get('/', [ReportController::class, 'index'])->name('index');
    Route::get('approved', [ReportController::class, 'approved'])->name('approved');
    Route::get('rejected', [ReportController::class, 'rejected'])->name('rejected');
    Route::get('trial-summary', [ReportController::class, 'trialSummary'])->name('trial-summary');
    Route::get('department-review', [ReportController::class, 'departmentReview'])->name('department-review');
    Route::get('audit-print-log', [ReportController::class, 'auditPrintLog'])->name('audit-print-log');
});
