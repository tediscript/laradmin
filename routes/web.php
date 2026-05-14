<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    Route::middleware('permission:post.view')->group(function () {
        Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    });
    Route::middleware('permission:post.create')->group(function () {
        Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('posts', [PostController::class, 'store'])->name('posts.store');
    });
    Route::middleware('permission:post.view')->group(function () {
        Route::get('posts/{post}', [PostController::class, 'show'])->name('posts.show');
    });
    Route::middleware('permission:post.update')->group(function () {
        Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::patch('posts/{post}', [PostController::class, 'update'])->name('posts.update');
    });
    Route::delete('posts/{post}', [PostController::class, 'destroy'])
        ->middleware('permission:post.delete')
        ->name('posts.destroy');
    Route::resource('users', UserController::class);

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
