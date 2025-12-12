<?php

declare(strict_types=1);

use Awcodes\Curator\Config\GlideManager;
use Awcodes\Curator\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix(app(GlideManager::class)->getBasePath())
    ->get('/{path}', [MediaController::class, 'show'])
    ->where('path', '.*');
