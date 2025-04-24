<?php

use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

Route::controller(TagController::class)
    ->prefix('/tags')
    ->group(function () {
        Route::get('/trending', 'getTrendingTags');
        Route::get('/search', 'search');
        Route::get('/categories', 'getTags');
    });
