<?php

use Awcodes\Curator\Http\Controllers\MediaController;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

$panel = Filament::getCurrentPanel();

if ($panel) {
    $domains = $panel->getDomains();
    
    foreach ((empty($domains) ? [null] : $domains) as $domain) {
        Route::domain($domain)
            ->middleware($panel->getMiddleware())
            ->group(function () {
                Route::get('/curator/media', [MediaController::class, 'index']);
                Route::get('/curator/media/search', [MediaController::class, 'search']);
            });
    }
}

Route::get('/curator/{path}', [MediaController::class, 'show'])
    ->where('path', '.*');
