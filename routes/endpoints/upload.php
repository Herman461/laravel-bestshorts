<?php

use App\Http\Controllers\UploadVideoController;
use Illuminate\Support\Facades\Route;

Route::controller(UploadVideoController::class)
    ->group(function () {
        Route::post('/videos/upload', 'upload');
    });
