<?php

use App\Http\Controllers\Admin\AccessRightController;
use App\Http\Controllers\Admin\MasterOptionController;
use App\Http\Controllers\Admin\ParameterController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('users', [UserController::class, 'store'])->name('users.store');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::post('products', [ProductController::class, 'store'])->name('products.store');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    Route::get('parameters', [ParameterController::class, 'index'])->name('parameters.index');
    Route::post('parameters', [ParameterController::class, 'store'])->name('parameters.store');
    Route::delete('parameters/{parameter}', [ParameterController::class, 'destroy'])->name('parameters.destroy');

    Route::get('masters', [MasterOptionController::class, 'index'])->name('masters.index');
    Route::post('masters', [MasterOptionController::class, 'store'])->name('masters.store');
    Route::delete('masters/{masterOption}', [MasterOptionController::class, 'destroy'])->name('masters.destroy');

    Route::get('access-rights', [AccessRightController::class, 'index'])->name('access-rights.index');
    Route::post('access-rights/users/{user}/role', [AccessRightController::class, 'updateRole'])->name('access-rights.users.role');
    Route::post('access-rights/reviewer-departments', [AccessRightController::class, 'storeReviewerDepartment'])->name('access-rights.reviewer-departments.store');
    Route::delete('access-rights/reviewer-departments/{reviewerDepartment}', [AccessRightController::class, 'destroyReviewerDepartment'])->name('access-rights.reviewer-departments.destroy');
    Route::post('access-rights/draft-permissions', [AccessRightController::class, 'grantPermission'])->name('access-rights.draft-permissions.store');
    Route::post('access-rights/draft-permissions/{permission}/revoke', [AccessRightController::class, 'revokePermission'])->name('access-rights.draft-permissions.revoke');
});
