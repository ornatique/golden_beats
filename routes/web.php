<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes(); // 🔥 THIS ENABLES /login, /register, /password/reset

Route::get('/home', [HomeController::class, 'index'])
    ->middleware('auth')
    ->name('home');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
