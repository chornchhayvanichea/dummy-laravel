<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    // get current user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // protected card routes
    Route::prefix('cards')->group(function () {
        Route::get('/', [CardController::class,'index']);
        Route::get('/{card}', [CardController::class,'show']);
        Route::post('/', [CardController::class,'store']);
        Route::put('/{card}', [CardController::class,'update']);
        Route::delete('/{card}', [CardController::class,'destroy']);
    });

    // logout route (protected)
    Route::post('/logout', [AuthController::class,'logout']);
});
