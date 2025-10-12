<?php

use App\Http\Controllers\UtilisateurController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [UtilisateurController::class, 'store']);
Route::post('/login', [UtilisateurController::class, 'login']);

Route::post('/user/verify', [UtilisateurController::class, 'verify']);