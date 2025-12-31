<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('users/data', [UserController::class, 'getData'])
        ->middleware('permission:user-view')
        ->name('users.data');
    Route::resource('users', UserController::class)
        ->middleware('permission:user-view')
        ->except(['show']);
    Route::get('users/create', [UserController::class, 'create'])
        ->middleware('permission:user-create')
        ->name('users.create');

    Route::post('users', [UserController::class, 'store'])
        ->middleware('permission:user-create')
        ->name('users.store');

    Route::get('users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('permission:user-edit')
        ->name('users.edit');

    Route::put('users/{user}', [UserController::class, 'update'])
        ->middleware('permission:user-edit')
        ->name('users.update');

    Route::get('users/export/excel', [UserController::class, 'exportExcel'])
        ->middleware('permission:user-export')
        ->name('users.export.excel');

    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
});

