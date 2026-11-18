<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Workbench\App\Http\Controllers\PostController;

Route::redirect('/', '/admin');

Route::get('/posts', [PostController::class, 'index'])->name('workbench.posts.index');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('workbench.posts.show');
