<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});


Route::get('/users', [\App\Http\Controllers\UserController::class, 'index']);
Route::post('/users', [\App\Http\Controllers\UserController::class, 'store']);
Route::get('/users/{id}/{name}', [\App\Http\Controllers\UserController::class, 'show']);
Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update']);
Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy']);



Route::get('/uploading', [\App\Http\Controllers\UserController::class, 'uploading']);
Route::get('/download', [\App\Http\Controllers\UserController::class, 'download']);
Route::get('/listBuckets', [\App\Http\Controllers\UserController::class, 'listBuckets']);

//状态码返回
Route::get('/testCode', [\App\Http\Controllers\ApiController::class, 'index']);
Route::post('/testCode', [\App\Http\Controllers\ApiController::class, 'store']);
Route::post('/testCode', [\App\Http\Controllers\ApiController::class, 'show']);
Route::put('/testCode/{id}', [\App\Http\Controllers\ApiController::class, 'update']);
Route::delete('/testCode/{id}', [\App\Http\Controllers\ApiController::class, 'destroy']);

