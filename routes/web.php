<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SavedPostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/bai-viet/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::get('/bai-viet/{post:slug}/comments', [CommentController::class, 'index'])
    ->name('comments.index');
Route::middleware(['auth', 'throttle:10,1'])->group(function () {
    Route::post('/bai-viet/{post:slug}/comments', [CommentController::class, 'store'])
        ->name('comments.store');
    Route::delete('/bai-viet/{post:slug}/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');
});
Route::middleware('auth')->group(function () {
    Route::get('/tai-khoan', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/tai-khoan/bai-viet-da-luu', [ProfileController::class, 'savedPosts'])
        ->name('profile.saved');
    Route::get('/tai-khoan/lich-su-da-doc', [ProfileController::class, 'readingHistory'])
        ->name('profile.history');
    Route::post('/bai-viet/{post:slug}/luu', [SavedPostController::class, 'toggle'])
        ->name('saved-posts.toggle');
});
Route::patch('/bai-viet/{post:slug}/comments/{comment}/approval', [
    CommentController::class,
    'updateApproval',
])->middleware(['auth', 'admin'])->name('comments.approval');
Route::get('/categories/{slug}', [HomeController::class, 'category'])->name('categories.show');
Route::get('/search', [HomeController::class, 'search'])->name('search');

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

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('comments', [AdminCommentController::class, 'index'])->name('comments.index');
        Route::resource('categories', AdminCategoryController::class)->except(['show']);
        Route::post('posts/fetch-rss', [AdminPostController::class, 'fetchRss'])->name('posts.fetch-rss');
        Route::resource('posts', AdminPostController::class)->except(['show']);
    });
