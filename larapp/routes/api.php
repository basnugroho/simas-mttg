<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\MosqueController;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\PrayerTimeController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\DashboardController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('auth/logout', [AuthController::class, 'logout']);

// Public
Route::get('regions', [RegionController::class, 'index']);
Route::get('mosques', [MosqueController::class, 'index']);
Route::get('mosques/{id}', [MosqueController::class, 'show']);
Route::get('mosques/{id}/facilities', [MosqueController::class, 'facilities']);
Route::get('facilities', [FacilityController::class, 'index']);
Route::get('facilities/overview', [FacilityController::class, 'overview']);

// Admin (protected)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('mosques', MosqueController::class)->except(['index', 'show']);
    Route::put('mosques/{id}/facilities', [MosqueController::class, 'updateFacilities']);
});

