<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::controller(VideoController::class)
    ->group(function () {
        Route::get('/video/video-info/{video}', 'getVideoInfo');
        Route::get('/video/{video}', 'getVideo');
        Route::delete('/video/delete/{video}', 'delete');

        Route::get('/videos/comments/{slug}', 'getCommentsByVideoId');
        Route::post('/videos/set-like/{slug}', 'setLike');
        Route::get('/videos/feed', 'getRandomShorts');
        Route::get('/videos/increment-views/{slug}', 'incrementViews');
        Route::get('/videos/category', 'getCategoryShorts');

    });
