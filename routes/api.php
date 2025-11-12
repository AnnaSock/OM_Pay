<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ClientController;
use App\Http\Controllers\Api\V1\CompteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


// Authentification
Route::post('/sendOtp', [ClientController::class, 'sendOtp']);
Route::post('/verifyOtp', [ClientController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login']);

// Comptes
Route::post('/comptes', [CompteController::class, 'store']);
// Routes protégées
Route::middleware('auth:api-client')->group(function () {
    Route::get('/me', [ClientController::class, 'me']);
});




