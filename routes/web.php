<?php

use Illuminate\Support\Facades\Route;
use FilamentCurator\Http\Controllers\MediaController;

Route::domain(config("filament.domain"))
    ->middleware(config("filament.middleware.base"))
    ->group(function () {
        Route::get('/filament-curator/media', [MediaController::class, 'index']);
        Route::get('/filament-curator/media/search', [MediaController::class, 'search']);
    });
