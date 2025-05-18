<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\UserMedicationController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

use App\Http\Controllers\DrugSearchController;

Route::get('/search', [DrugSearchController::class, 'search']);

Route::middleware('auth:sanctum')->prefix('user')->group(function () {
    Route::get('drugs', [UserMedicationController::class, 'index']);
    Route::post('drugs', [UserMedicationController::class, 'store']);
    Route::delete('drugs/{rxcui}', [UserMedicationController::class, 'destroy']);
});
