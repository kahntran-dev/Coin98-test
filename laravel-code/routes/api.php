<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::get('/upload');
//Route::post('/retrieve/{filename}');
//Route::delete('/delete/{filename}');

Route::controller(FileController::class)->group(function () {
    Route::get('/retrieve/{filename}', 'retrieve');
    Route::post('/upload', 'upload');
    Route::delete('/delete/{filename}', 'delete');
});
