<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyPhotoController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;

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


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/bookings', [BookingController::class, 'store']);  // Réserver un logement
    Route::get('/bookings', [BookingController::class, 'index']);   // Voir les réservations
    Route::patch('/bookings/{id}/cancel', [BookingController::class, 'cancel']);  // Annuler une réservation
    Route::patch('/bookings/{id}/status', [BookingController::class, 'updateStatus']); // Approuver/Rejeter
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/properties/{propertyId}/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
});