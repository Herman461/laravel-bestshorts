<?php


use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::controller(UserController::class)
    ->prefix('/user')
    ->group(function() {
        Route::get('/fullname/{username}', 'getFullname');
        Route::get('/single/{user}', 'getUser');
        Route::get('/user-page/{user}', 'getUserPage');
        Route::get('/followers/{user}', 'getFollowers');
        Route::get('/following/{user}', 'getFollowing');
        Route::get('/feed/categories', 'getCreatorsByCategory');

        Route::post('/set-follow/{username}', 'setFollow');
        Route::post('/edit', 'edit');
    });
