<?php

use FilamentCurator\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::domain(config('filament.domain'))
    ->middleware(config('filament.middleware.base'))
    ->group(function () {
        Route::get('/filament-curator/media', [MediaController::class, 'index']);
        Route::get('/filament-curator/media/search', [MediaController::class, 'search']);
    });
