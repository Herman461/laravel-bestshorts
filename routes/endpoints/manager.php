<?php


use App\Http\Controllers\ManagerController;
use Illuminate\Support\Facades\Route;

Route::controller(ManagerController::class)
    ->prefix('/manager')
    ->group(function() {
        Route::delete('/delete-video', 'delete');
    });
