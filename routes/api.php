<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CirculationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BookCopyController;
use App\Http\Controllers\Api\AuthorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//  User Authentication
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Member routes
Route::middleware('auth:sanctum')->get('/members', [UserController::class, 'getAllMembers']);
Route::middleware('auth:sanctum')->get('/members/{id}', [UserController::class, 'getMember']);

Route::middleware('auth:sanctum')->post('/members', [UserController::class, 'createMember']);
Route::middleware('auth:sanctum')->delete('/members/{member}', [UserController::class, 'deleteMember']);

// Authentication
Route::post('/login', [AuthController::class, 'loginUser']);
Route::post('/logout', [AuthController::class, 'logoutUser']);
Route::post('/register', [AuthController::class, 'createUser']);

// Book CRUD
Route::apiResource("books",'\App\Http\Controllers\Api\BookController')->middleware('auth:sanctum');

// Author CRUD
Route::post('/authors/multiple', [AuthorController::class, 'showMultipleAuthors']);

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

// Additional book Copy and Circulation Routes
Route::middleware('auth:sanctum')->get(
    '/book-copies/{barcode}',
    [BookCopyController::class, 'getByBarcode']
);

Route::middleware('auth:sanctum')->get(
    '/circulation/allLoans',
    [CirculationController::class, 'getAllLoans']
);

Route::middleware('auth:sanctum')->get(
    '/circulation/allOverdue',
    [CirculationController::class, 'getAllOverdue']
);

Route::middleware('auth:sanctum')->get(
    '/circulation/allReturns',
    [CirculationController::class, 'getAllReturns']
);

Route::middleware('auth:sanctum')->get(
    '/circulation/activeLoans',
    [CirculationController::class, 'getActiveLoans']
);
