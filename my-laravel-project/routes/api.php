<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyAdvertisementController;

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

// Public routes
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // User & authentication routes
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/tokens', [AuthController::class, 'tokens']);
    Route::delete('/tokens/{tokenId}', [AuthController::class, 'revokeToken']);
    
    // Company advertisement routes
    Route::get('/company-advertisements', [CompanyAdvertisementController::class, 'index']);
    Route::get('/company-advertisements/{id}', [CompanyAdvertisementController::class, 'show']);
    Route::post('/company-advertisements', [CompanyAdvertisementController::class, 'store']);
    Route::put('/company-advertisements/{id}', [CompanyAdvertisementController::class, 'update']);
    Route::delete('/company-advertisements/{id}', [CompanyAdvertisementController::class, 'destroy']);
});
