<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\{
    UserController,
    RoleController,
    PermissionController,
    CategoryController,
    SubcategoryController,
    ProductController,
    OrderController,
    CustomOrderController,
    PopupBannerAdController,
    BannerAdController,
    SocialMediaController,
    ReelController
};

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:user-view')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/data', [UserController::class, 'getData'])->name('users.data');
        Route::get('users/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');
    });

    Route::middleware('permission:user-create')->group(function () {
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
    });

    Route::middleware('permission:user-edit')->group(function () {
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('users/status', [UserController::class, 'updateStatus'])->name('users.status');
        Route::post('check-email', [UserController::class, 'checkEmail'])->name('check.email');
    });

    Route::middleware('permission:user-delete')->group(function () {
        Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | ROLES & PERMISSIONS
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:role-view')->group(function () {
        Route::resource('roles', RoleController::class)->except(['show']);
    });

    Route::middleware('permission:permission-view')->group(function () {
        Route::resource('permissions', PermissionController::class)->except(['show']);
    });

    /*
    |--------------------------------------------------------------------------
    | CATEGORIES
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:category-view')->group(function () {
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('categories/data', [CategoryController::class, 'getData'])->name('categories.data');
    });

    Route::middleware('permission:category-create')->group(function () {
        Route::get('categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('categories', [CategoryController::class, 'store'])->name('categories.store');
    });

    Route::middleware('permission:category-edit')->group(function () {
        Route::get('categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    });

    Route::middleware('permission:category-delete')->group(function () {
        Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });
    Route::get('get-subcategories_data/{category}', [ProductController::class, 'getSubcategories_data'])
        ->name('get.subcategories_data');
    /*
    |--------------------------------------------------------------------------
    | SUBCATEGORIES
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:subcategory-view')->group(function () {
        Route::get('subcategories', [SubcategoryController::class, 'index'])->name('subcategories.index');
        Route::get('subcategories/data', [SubcategoryController::class, 'getData'])->name('subcategories.data');
    });

    Route::middleware('permission:subcategory-create')->group(function () {
        Route::get('subcategories/create', [SubcategoryController::class, 'create'])->name('subcategories.create');
        Route::post('subcategories', [SubcategoryController::class, 'store'])->name('subcategories.store');
    });

    Route::middleware('permission:subcategory-edit')->group(function () {
        Route::get('subcategories/{subcategory}/edit', [SubcategoryController::class, 'edit'])->name('subcategories.edit');
        Route::put('subcategories/{subcategory}', [SubcategoryController::class, 'update'])->name('subcategories.update');
    });

    Route::middleware('permission:subcategory-delete')->group(function () {
        Route::delete('subcategories/{subcategory}', [SubcategoryController::class, 'destroy'])->name('subcategories.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:product-view')->group(function () {
        Route::get('products', [ProductController::class, 'index'])->name('products.index');
        Route::get('products/data', [ProductController::class, 'data'])->name('products.data');
        Route::get('products/{product}/qr', [ProductController::class, 'qrPreview'])->name('products.qr');
        Route::get('products/{product}/qr-pdf', [ProductController::class, 'qrPdf'])->name('products.qr.pdf');
    });

    Route::middleware('permission:product-create')->group(function () {
        Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
        Route::post('products', [ProductController::class, 'store'])->name('products.store');
    });

    Route::middleware('permission:product-edit')->group(function () {
        Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
        Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    });

    Route::middleware('permission:product-delete')->group(function () {
        Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    });
    Route::get(
        'get-subcategories_product/{category}',
        [ProductController::class, 'getSubcategories']
    )->name('get.subcategories');
    Route::post('products/bulk-pdf', [ProductController::class, 'bulkPdf'])
        ->name('products.bulk.pdf');
    Route::post('products-details/bulk-pdf', [ProductController::class, 'bulkPdfdetail'])
        ->name('products-details.bulk.pdf');
    Route::get(
        'products/print/qrcode',
        [App\Http\Controllers\Admin\ProductController::class, 'printQr']
    )->name('products.print.qrcode');

    /*
    |--------------------------------------------------------------------------
    | ORDERS
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:order-view')->group(function () {
        Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/data', [OrderController::class, 'getData'])->name('orders.data');
        Route::get('orders/{order}/print', [OrderController::class, 'print'])->name('orders.print');
        Route::get('orders/{order}/pdf', [OrderController::class, 'pdf'])->name('orders.pdf');
    });

    Route::middleware('permission:order-edit')->group(function () {
        Route::get('orders/{order}/edit', [OrderController::class, 'edit'])->name('orders.edit');
        Route::put('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
        Route::post('orders/update-status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    });

    Route::middleware('permission:order-delete')->group(function () {
        Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | CUSTOM ORDERS
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:custom-order-view')->group(function () {
        Route::get('custom-orders', [CustomOrderController::class, 'index'])->name('custom-orders.index');
        Route::get('custom-orders/data', [CustomOrderController::class, 'data'])->name('custom-orders.data');
        Route::get('custom-orders/{customOrder}/print', [CustomOrderController::class, 'print'])->name('custom-orders.print');
    });

    Route::middleware('permission:custom-order-edit')->group(function () {
        Route::get('custom-orders/{customOrder}/edit', [CustomOrderController::class, 'edit'])->name('custom-orders.edit');
        Route::put('custom-orders/{customOrder}', [CustomOrderController::class, 'update'])->name('custom-orders.update');
        Route::post('custom-orders/{customOrder}/status', [CustomOrderController::class, 'updateStatus'])->name('custom-orders.status');
    });

    /*
    |--------------------------------------------------------------------------
    | BANNER ADS
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:banner-ad-view')->group(function () {
        Route::get('banner-ads', [BannerAdController::class, 'index'])->name('banner-ads.index');
        Route::get('banner-ads/data', [BannerAdController::class, 'data'])->name('banner-ads.data');
    });

    Route::middleware('permission:banner-ad-create')->group(function () {
        Route::get('banner-ads/create', [BannerAdController::class, 'create'])->name('banner-ads.create');
        Route::post('banner-ads', [BannerAdController::class, 'store'])->name('banner-ads.store');
    });

    Route::middleware('permission:banner-ad-edit')->group(function () {
        Route::get('banner-ads/{bannerAd}/edit', [BannerAdController::class, 'edit'])->name('banner-ads.edit');
        Route::put('banner-ads/{bannerAd}', [BannerAdController::class, 'update'])->name('banner-ads.update');
    });

    Route::middleware('permission:banner-ad-delete')->group(function () {
        Route::delete('banner-ads/{bannerAd}', [BannerAdController::class, 'destroy'])->name('banner-ads.destroy');
    });


    Route::get(
        'get-subcategories/{category}',
        [BannerAdController::class, 'getSubcategories']
    )->name('get-subcategories');

    Route::get(
        'get-products/{subcategory}',
        [BannerAdController::class, 'getProducts']
    )->name('get-products');

    /*
    |--------------------------------------------------------------------------
    | POPUP BANNER ADS
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:popup-ad-view')->group(function () {
        Route::get('popup-banner-ads', [PopupBannerAdController::class, 'index'])->name('popup-banner-ads.index');
        Route::get('popup-banner-ads/data', [PopupBannerAdController::class, 'data'])->name('popup-banner-ads.data');
    });

    Route::middleware('permission:popup-ad-edit')->group(function () {
        Route::get('popup-banner-ads/{popupBannerAd}/edit', [PopupBannerAdController::class, 'edit'])->name('popup-banner-ads.edit');
        Route::put('popup-banner-ads/{popupBannerAd}', [PopupBannerAdController::class, 'update'])->name('popup-banner-ads.update');
    });

    /*
    |--------------------------------------------------------------------------
    | SOCIAL MEDIA
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:social-media-edit')->group(function () {
        Route::get('social-media', [SocialMediaController::class, 'index'])->name('social-media.index');
        Route::post('social-media', [SocialMediaController::class, 'update'])->name('social-media.update');
    });

    /*
    |--------------------------------------------------------------------------
    | REELS
    |--------------------------------------------------------------------------
    */
    Route::middleware('permission:reel-view')->group(function () {
        Route::get('reels', [ReelController::class, 'index'])->name('reels.index');
        Route::get('reels/data', [ReelController::class, 'data'])->name('reels.data');
        Route::get('reels/{reel}/comments', [ReelController::class, 'comments'])->name('reels.comments');
    });

    Route::middleware('permission:reel-create')->group(function () {
        Route::get('reels/create', [ReelController::class, 'create'])->name('reels.create');
        Route::post('reels', [ReelController::class, 'store'])->name('reels.store');
    });

    Route::middleware('permission:reel-edit')->group(function () {
        Route::get('reels/{reel}/edit', [ReelController::class, 'edit'])->name('reels.edit');
        Route::put('reels/{reel}', [ReelController::class, 'update'])->name('reels.update');
        Route::put('reel-comments/{comment}', [ReelController::class, 'updateComment'])->name('reel-comments.update');
    });

    Route::middleware('permission:reel-delete')->group(function () {
        Route::delete('reels/{reel}', [ReelController::class, 'destroy'])->name('reels.destroy');
        Route::delete('reel-comments/{comment}', [ReelController::class, 'deleteComment'])->name('reel-comments.delete');
    });
});
