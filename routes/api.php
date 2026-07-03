<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\QRVerifyController;
use App\Http\Controllers\API\AIController;
use App\Http\Controllers\Public\FoodController;

// Public API
Route::get('/foods',           [FoodController::class, 'apiIndex']);
Route::get('/foods/{id}',      [FoodController::class, 'apiShow']);
Route::get('/categories',      [\App\Http\Controllers\Public\HomeController::class, 'categories']);

// Authenticated Business API
Route::middleware(['auth:sanctum', 'role:business_owner'])->group(function () {
    Route::post('/qr/verify',      [QRVerifyController::class, 'verify'])->name('api.qr.verify');
    Route::post('/ai/expiry-risk', [AIController::class, 'expiryRisk']);
    Route::post('/ai/discount',    [AIController::class, 'discount']);
    Route::get('/ai/forecast/{businessId}', [AIController::class, 'forecast']);
});
