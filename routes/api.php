<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiagnosisController;
use App\Http\Controllers\SymptomController;
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

// Auth related routes
Route::prefix('/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {
    // Symptoms routes
    Route::prefix('/symptoms')->group(function () {
        Route::get('/', [SymptomController::class, 'index']);
    });

    // Diagnosis routes
    Route::prefix('/diagnosis')->group(function () {
        Route::get('/', [DiagnosisController::class, 'getDiagnosis']);
        Route::patch('/confirm', [DiagnosisController::class, 'confirmDiagnosis']);
        Route::get('/history', [DiagnosisController::class, 'index']);
    });
});
