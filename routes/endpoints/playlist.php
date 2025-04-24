<?php

use App\Http\Controllers\PlaylistController;
use Illuminate\Support\Facades\Route;

Route::controller(PlaylistController::class)
    ->prefix('/playlist')
    ->group(function () {
        Route::get('/{playlist}/videos', 'getVideos');
        Route::get('/video/{video}', 'getVideoPlaylists');
        Route::get('/all', 'getUserPlaylists');
        Route::get('/single/{playlist}', 'getSinglePlaylist');

        Route::post('/create', 'create');
        Route::post('/update/{playlist}', 'update');
        Route::post('/{playlist}/add/video/{video}', 'addVideoToPlaylist');
        Route::post('/{playlist}/delete/videos', 'deleteVideosFromPlaylist');

        Route::delete('/delete/{playlist}', 'delete');
        Route::delete('/{playlist}/delete/video/{video}', 'deleteVideoFromPlaylist');
    });
