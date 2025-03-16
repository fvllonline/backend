<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyPhotoController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);


Route::middleware('auth:sanctum')->get('/user', [UserProfileController::class, 'show']);
Route::middleware('auth:sanctum')->put('/user', [UserProfileController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/user', [UserProfileController::class, 'destroy']);


Route::get('/properties', [PropertyController::class, 'index']);
Route::get('/properties/{id}', [PropertyController::class, 'show']);
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/properties', [PropertyController::class, 'store']);
    Route::put('/properties/{id}', [PropertyController::class, 'update']);
    Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);
});


Route::post('/properties/{property_id}/photos', [PropertyPhotoController::class, 'store']);  // Ajouter une photo
Route::get('/properties/{property_id}/photos', [PropertyPhotoController::class, 'index']);   // Récupérer les photos
Route::delete('/photos/{id}', [PropertyPhotoController::class, 'destroy']);                  // Supprimer une photo