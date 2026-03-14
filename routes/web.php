<?php

use App\Http\Controllers\AIController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\UserPostController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('posts.index'));

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create')->middleware(['auth', 'verified']);
Route::post('/posts', [PostController::class, 'store'])->name('posts.store')->middleware(['auth', 'verified'])->middleware('throttle:10,1');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::post('/posts/{post}/like', [PostLikeController::class, 'toggle'])->name('posts.like')->middleware(['auth', 'verified'])->middleware('throttle:60,1');
Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit')->middleware(['auth', 'verified']);
Route::patch('/posts/{post}', [PostController::class, 'update'])->name('posts.update')->middleware(['auth', 'verified']);
Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy')->middleware(['auth', 'verified']);

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::get('/my-posts', [UserPostController::class, 'index'])->name('posts.my-posts');

    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store')->middleware('throttle:30,1');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
    Route::post('/comments/{comment}/flag', [CommentController::class, 'flag'])->name('comments.flag')->middleware('throttle:10,1');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // AI Routes - Rate limited to prevent abuse
    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/status', [AIController::class, 'status'])->name('status');
        Route::post('/generate', [AIController::class, 'generateContent'])->name('generate')->middleware('throttle:10,1');
        Route::post('/titles', [AIController::class, 'generateTitles'])->name('titles')->middleware('throttle:20,1');
        Route::post('/outline', [AIController::class, 'generateOutline'])->name('outline')->middleware('throttle:20,1');
        Route::post('/improve', [AIController::class, 'improveWriting'])->name('improve')->middleware('throttle:15,1');
        Route::post('/seo', [AIController::class, 'generateSEO'])->name('seo')->middleware('throttle:20,1');
        Route::post('/moderate', [AIController::class, 'moderateContent'])->name('moderate')->middleware('throttle:30,1');
        Route::post('/summarize', [AIController::class, 'summarize'])->name('summarize')->middleware('throttle:15,1');
        Route::post('/hashtags', [AIController::class, 'generateHashtags'])->name('hashtags')->middleware('throttle:20,1');
        Route::post('/tone', [AIController::class, 'changeTone'])->name('tone')->middleware('throttle:15,1');
        Route::post('/expand', [AIController::class, 'expandContent'])->name('expand')->middleware('throttle:10,1');
    });
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/ban', [UserController::class, 'ban'])->name('users.ban');
    Route::post('/users/{user}/unban', [UserController::class, 'unban'])->name('users.unban');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');

    Route::get('/posts', [AdminPostController::class, 'index'])->name('posts.index');
    Route::get('/posts/{post}', [AdminPostController::class, 'show'])->name('posts.show');
    Route::get('/posts/{post}/edit', [AdminPostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [AdminPostController::class, 'update'])->name('posts.update');
    Route::post('/posts/{post}/approve', [AdminPostController::class, 'approve'])->name('posts.approve');
    Route::post('/posts/{post}/archive', [AdminPostController::class, 'archive'])->name('posts.archive');
    Route::delete('/posts/{post}', [AdminPostController::class, 'destroy'])->name('posts.destroy');
    Route::post('/posts/bulk-action', [AdminPostController::class, 'bulkAction'])->name('posts.bulk-action');

    Route::get('/comments', [AdminCommentController::class, 'index'])->name('comments.index');
    Route::post('/comments/{comment}/archive', [AdminCommentController::class, 'archive'])->name('comments.archive');
    Route::post('/comments/{comment}/restore', [AdminCommentController::class, 'restore'])->name('comments.restore');
    Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');

    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

require __DIR__.'/auth.php';
