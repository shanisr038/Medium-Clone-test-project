<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\PublicProfileController;
use App\Http\Controllers\ClapController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\CategoryController;
// Public Routes
Route::get('/', [PostController::class, 'index'])->name('dashboard');
Route::get('/@{user:username}', [PublicProfileController::class, 'show'])->name('profile.show');
Route::get('/@{user:username}/{post:slug}', [PostController::class, 'show'])->name('post.show');
Route::get('/category/{category:slug}', [PostController::class, 'category'])->name('category.show');
Route::post('/posts/{post}/clap', [ClapController::class, 'toggle'])->middleware('auth')->name('posts.clap');

// Authenticated Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/my-posts', [PostController::class, 'myPosts'])->name('my.posts');
    Route::post('/users/{user}/toggle-follow', [FollowerController::class, 'toggleFollow'])->name('user.toggleFollow');
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->middleware('can:update,post')->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->middleware('can:update,post')->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->middleware('can:delete,post')->name('posts.destroy');

    // Comments
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::post('/comments/{comment}/like', [CommentController::class, 'toggleLike'])->name('comments.like');
});

// Profile
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('category.show');

// Auth routes
require __DIR__ . '/auth.php';
