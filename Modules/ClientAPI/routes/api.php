<?php

use Illuminate\Support\Facades\Route;
use Modules\ClientAPI\app\Http\Controllers\AuthController;
use Modules\ClientAPI\app\Http\Controllers\CompanyController;
use Modules\ClientAPI\app\Http\Controllers\ProjectController;
use Modules\ClientAPI\app\Http\Controllers\MarketplaceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    // Public routes
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    
    Route::get('services', [MarketplaceController::class, 'services']);
    Route::get('services/{id}/providers', [MarketplaceController::class, 'providers']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('profile', [AuthController::class, 'updateProfile']);
        
        Route::apiResource('companies', CompanyController::class);
        
        Route::get('projects', [ProjectController::class, 'index']);
        Route::get('projects/{id}', [ProjectController::class, 'show']);
        Route::post('projects', [ProjectController::class, 'store']);
        Route::post('projects/{id}/messages', [ProjectController::class, 'sendMessage']);
        Route::post('projects/{id}/documents', [ProjectController::class, 'uploadDocument']);
        
        Route::get('subscriptions', [ProjectController::class, 'subscriptions']);
        Route::get('transactions', [ProjectController::class, 'transactions']);
    });
});
