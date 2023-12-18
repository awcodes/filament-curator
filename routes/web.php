<?php

use Awcodes\Curator\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('curator.glide.route_path', 'curator'))
    ->get('/{path}', [MediaController::class, 'show'])
    ->where('path', '.*');
