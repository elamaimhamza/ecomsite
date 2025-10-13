<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UtilisateurController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UtilisateurController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/verify', [AuthController::class, 'verify']);

Route::middleware('auth.api')->group(function () {
    Route::get('/user', [UtilisateurController::class, 'show']);
    Route::put('/user', [UtilisateurController::class, 'update']);
});
