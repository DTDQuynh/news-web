<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/bai-viet/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/categories/{slug}', [HomeController::class, 'category'])->name('categories.show');
Route::get('/search', fn () => abort(501, 'Search is not implemented yet.'))->name('search');

Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::view('/register', 'auth.register')->name('register');

    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login.store');
        Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    });
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/admin', [AdminController::class, 'dashboard'])
    ->middleware('auth')
    ->name('admin.dashboard');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    });
