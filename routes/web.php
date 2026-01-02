<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RoleController;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes(); // 🔥 THIS ENABLES /login, /register, /password/reset

Route::get('/home', [HomeController::class, 'index'])
    ->middleware('auth')
    ->name('home');
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});

Route::group([
    'prefix' => 'admin',
    'middleware' => ['auth', 'role:admin']
], function () {

    // Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::resource('roles', App\Http\Controllers\Admin\RoleController::class);
    Route::resource('permissions', App\Http\Controllers\Admin\PermissionController::class);

});

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


require __DIR__.'/admin.php';