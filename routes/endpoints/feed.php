<?php

use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Route;

Route::controller(FeedController::class)
    ->group(function () {
        Route::get('/feed/videos', 'getVideos');
    });
