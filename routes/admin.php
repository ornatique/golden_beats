<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\SubcategoryController;
use App\Http\Controllers\Admin\ProductController;



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
        Route::post('check-email', [UserController::class, 'checkEmail'])
            ->middleware('permission:user-export')
            ->name('check.email');
        Route::post('users/status', [UserController::class, 'updateStatus'])
            ->name('users.status');

        Route::get('categories/data', [CategoryController::class, 'getData'])
            ->name('categories.data');
        Route::get(
            'subcategories/data',
            [SubcategoryController::class, 'getData']
        )->name('subcategories.data');

        Route::get('products/data', [ProductController::class, 'data'])->name('products.data');
        Route::get(
            'get-subcategories/{category}',
            [ProductController::class, 'getSubcategories']
        )->name('get.subcategories');

        // QR preview (image)
        Route::get('products/{product}/qr', [ProductController::class, 'qrPreview'])
            ->name('products.qr');

        // QR PDF
        Route::get('products/{product}/qr-pdf', [ProductController::class, 'qrPdf'])
            ->name('products.qr.pdf');


        Route::resource('products', ProductController::class);
        Route::resource('subcategories', SubcategoryController::class);
        Route::resource('categories', CategoryController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permissions', PermissionController::class);
    });
