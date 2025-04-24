<?php

use App\Http\Controllers\EmailController;
use App\Http\Controllers\HlsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

//Route::get('storage/{filename}', function ($filename) {
////    return response()->json([
////       'ok' => 'ok!'
////    ]);
//    $path = storage_path('app/public/' . $filename . '.m3u8');
//
//    if (file_exists($path)) {
//        $file = file_get_contents($path);
//        $type = mime_content_type($path);
//
//        return response($file, 200)->withHeaders(['Content-Type' => $type, 'Access-Control-Allow-Origin' => '*']);
//    } else {
//        return response('File not found', 404);
//    }
//});
//    dd($filename);
//    $path = storage_path('app/public/' . $filename);
//
//    if (file_exists($path)) {
//        $file = file_get_contents($path);
//        $type = mime_content_type($path);
//
//        return response($file, 200)->header('Content-Type', $type);
//    } else {
//        return response('File not found', 404);
//    }
//});

Route::get('/videos/{date}/{name}/{filename}', [HlsController::class, 'stream'])->where('filename', '.*\.(m3u8|ts|mp4)$');;

