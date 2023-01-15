<?php

use Awcodes\Curator\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::domain(config('filament.domain'))
    ->middleware((config('filament.middleware.base')))
    ->group(function () {
        Route::get('/curator/media', [MediaController::class, 'index']);
        Route::get('curator/media/search', [MediaController::class, 'search']);
    });

Route::get('/curator/{path}', [MediaController::class, 'show'])
    ->where('path', '.*');
