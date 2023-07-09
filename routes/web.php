<?php

use Awcodes\Curator\Http\Controllers\MediaController;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::domain(Filament::getCurrentPanel()->getDomain())
    ->middleware(Filament::getCurrentPanel()->getMiddleware())
    ->group(function () {
        Route::get('/curator/media', [MediaController::class, 'index']);
        Route::get('/curator/media/search', [MediaController::class, 'search']);
    });

Route::get('/curator/{path}', [MediaController::class, 'show'])
    ->where('path', '.*');
