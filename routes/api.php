<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\WishlistController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ReelController;
use App\Http\Controllers\Api\QrProductController;
use App\Http\Controllers\Api\NotificationController;

// Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('/profile', [AuthController::class, 'profile']); 


 Route::post('logout', [AuthController::class, 'logout']);


Route::get('states', [AuthController::class, 'states']);
Route::post('cities-by-state', [AuthController::class, 'citiesByState']);
Route::get('/dashboard', [AuthController::class, 'dashboard']);
Route::get('/products', [AuthController::class, 'products']);
Route::post(
    '/subcategories-by-category',
    [AuthController::class, 'subcategoriesByCategory']
);
Route::get('/product-details', [AuthController::class, 'productDetails']);
Route::get('/products/search', [AuthController::class, 'search']);

Route::post('/cart/add', [CartController::class, 'addToCart']);
Route::get('/cart', [CartController::class, 'cartList']);
Route::get('/cart/remove', [CartController::class, 'removeFromCart']);


Route::post('/wishlist/add', [WishlistController::class, 'add']);
Route::post('/wish-list', [WishlistController::class, 'list']);
Route::get('/wishlist/remove', [WishlistController::class, 'remove']);

Route::post('/order/place', [OrderController::class, 'placeOrder']);
Route::get('/orders', [OrderController::class, 'orderList']);

Route::post('/add-custom-orders', [OrderController::class, 'add_custom_order']);
Route::get('/list-custom-orders', [OrderController::class, 'list_custom_order']);

Route::get('/events-list', [EventController::class, 'event_list']);

Route::get('/reels-list', [ReelController::class, 'index']);
Route::get('/reels-details', [ReelController::class, 'reels_details']);
Route::post('/reels-like', [ReelController::class, 'toggleLike']);
Route::get('/reels-comments-list', [ReelController::class, 'comments_list']);
Route::post('/reels/add-comment', [ReelController::class, 'addComment']);
Route::get('/social-media-link', [ReelController::class, 'social_media_link']);

Route::get('/qr-products/list', [QrProductController::class, 'index']);
Route::post('/qr-products/save', [QrProductController::class, 'store']);
Route::delete('/qr-products/delete', [QrProductController::class, 'destroy']);

Route::get('/notifications', [NotificationController::class, 'index']);













