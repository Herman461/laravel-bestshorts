<?php

use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

Route::controller(CommentController::class)
    ->prefix('/comments')
    ->group(function() {
        Route::post('/create', 'create');
        Route::get('/video/{video}', 'getAll');
    });
