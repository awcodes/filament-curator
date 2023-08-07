<?php

use Awcodes\Curator\Http\Controllers\MediaController;
use Illuminate\Support\Facades\Route;

Route::get('/curator/{path}', [MediaController::class, 'show'])
    ->where('path', '.*');
