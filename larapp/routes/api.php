<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BitcoinController;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/bitcoin-price', [BitcoinController::class, 'getPrice']);
