<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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
