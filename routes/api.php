<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CirculationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BookCopyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Authentication
Route::post('/login', [AuthController::class, 'loginUser']);
Route::post('/logout', [AuthController::class, 'logoutUser']);
Route::post('/register', [AuthController::class, 'createUser']);

// Book CRUD
Route::apiResource("books",'\App\Http\Controllers\Api\BookController')->middleware('auth:sanctum');

// Author CRUD
Route::apiResource("authors", '\App\Http\Controllers\Api\AuthorController')->middleware('auth:sanctum');

// Circulation Routes
Route::middleware('auth:sanctum')->post(
    '/circulation/check-out/{book}',
    [CirculationController::class, 'checkOutBook']
);

Route::middleware('auth:sanctum')->post(
    '/circulation/check-in/{checkOut}',
    [CirculationController::class, 'checkInBook']
);

Route::middleware('auth:sanctum')->get(
    '/circulation/loans',
    [UserController::class,  'loans']
);


Route::middleware('auth:sanctum')->get(
    '/circulation/returns',
    [UserController::class,  'returns']
);

Route::middleware('auth:sanctum')->get(
    '/circulation/overdue',
    [UserController::class,  'overdue']
);

Route::middleware('auth:sanctum')->get(
    '/book-copies/{barcode}',
    [BookCopyController::class, 'getByBarcode']
);
