<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\TypeProduitController;
use App\Http\Controllers\UtilisateurController;
use Database\Seeders\ProduitSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/register', [UtilisateurController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/verify', [AuthController::class, 'verify']);

Route::middleware('auth.api')->group(function () {
    Route::get('/user', [UtilisateurController::class, 'show']);
    Route::put('/user', [UtilisateurController::class, 'update']);
});

Route::middleware(['auth.api', 'gestionnaire'])->group(function () {
    Route::put("/produit/{id}", [ProduitController::class, 'update']);
    Route::put("/admin/produits/{id}", [ProduitController::class, 'updateAdmin']);
    Route::get("/admin/produits", [ProduitController::class, 'getAll']);
    Route::post("/admin/produits", [ProduitController::class, 'store']);
    Route::delete("/admin/produits/{id}", [ProduitController::class, 'destroy']);
    Route::get("/admin/produits/types", [TypeProduitController::class, 'index']);
    Route::get("/admin/produits/genres", [GenreController::class, 'index']);
});

Route::post('/produits', [ProduitController::class, 'index']);
Route::post('/produits/list', [ProduitController::class, 'getProducts']);
Route::get('/produits/{id}', [ProduitController::class, 'show']);
