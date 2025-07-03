<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'loginUser']);
Route::post('/logout', [AuthController::class, 'logoutUser']);
Route::post('/register', [AuthController::class, 'createUser']);

Route::apiResource("books",'\App\Http\Controllers\Api\BookController')->middleware('auth:sanctum');

Route::apiResource("authors", '\App\Http\Controllers\Api\AuthorController')->middleware('auth:sanctum');
