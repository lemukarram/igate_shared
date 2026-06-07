<?php

use Illuminate\Support\Facades\Route;
use Modules\ClientAPI\Http\Controllers\AuthController;
use Modules\ClientAPI\Http\Controllers\CompanyController;
use Modules\ClientAPI\Http\Controllers\ProjectController;
use Modules\ClientAPI\Http\Controllers\MarketplaceController;

use Modules\ClientAPI\Http\Controllers\SettingsController;
use Modules\ClientAPI\Http\Controllers\PlanController;
use Modules\ClientAPI\Http\Controllers\TeamController;

use Modules\ClientAPI\Http\Controllers\PaymentController;

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
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    
    // Marketplace routes
    Route::get('categories', [MarketplaceController::class, 'categories']);
    Route::get('categories/{id}', [MarketplaceController::class, 'categoryDetail']);
    Route::get('services', [MarketplaceController::class, 'services']);
    Route::get('services/{id}', [MarketplaceController::class, 'serviceDetail']);
    Route::get('services/{id}/providers', [MarketplaceController::class, 'serviceProviders']);
    Route::get('providers/{id}', [MarketplaceController::class, 'providerDetail']);

    // Plans route
    Route::get('plans', [PlanController::class, 'index']);

    // Settings routes
    Route::prefix('settings')->group(function () {
        Route::get('general', [SettingsController::class, 'general']);
        Route::get('invoice', [SettingsController::class, 'invoice']);
        Route::get('payment', [SettingsController::class, 'payment']);
        Route::get('landing', [SettingsController::class, 'landing']);
    });

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('profile', [AuthController::class, 'updateProfile']);
        Route::post('logout', [AuthController::class, 'logout']);
        
        Route::apiResource('companies', CompanyController::class);
        
        // Team Management
        Route::get('team/members', [TeamController::class, 'index']);
        Route::post('team/members', [TeamController::class, 'store']);
        Route::delete('team/members/{id}', [TeamController::class, 'destroy']);
        
        Route::get('projects', [ProjectController::class, 'index']);
        Route::get('projects/{id}', [ProjectController::class, 'show']);
        Route::post('projects', [ProjectController::class, 'store']);
        Route::post('projects/{id}/messages', [ProjectController::class, 'sendMessage']);
        Route::get('projects/{id}/documents', [ProjectController::class, 'documents']);
        Route::post('projects/{id}/documents', [ProjectController::class, 'uploadDocument']);
        
        // Task Management
        Route::patch('projects/{id}/tasks/{taskId}/status', [ProjectController::class, 'updateTaskStatus']);
        Route::post('projects/{id}/tasks/{taskId}/verify', [ProjectController::class, 'verifyTask']);
        
        // Project Status Management
        Route::post('projects/{id}/approve', [ProjectController::class, 'approve']);
        Route::post('projects/{id}/reject', [ProjectController::class, 'reject']);
        Route::post('projects/{id}/cancel-request', [ProjectController::class, 'requestCancellation']);
        
        Route::get('subscriptions', [ProjectController::class, 'subscriptions']);
        Route::get('transactions', [ProjectController::class, 'transactions']);

        // Service Request & Payment flow
        Route::post('service-request', [PaymentController::class, 'serviceRequestDetails']);
        Route::post('checkout', [PaymentController::class, 'checkout']);
        Route::get('payment/verify/{transaction_id}', [PaymentController::class, 'verifyPayment']);
        Route::get('invoices/{id}', [PaymentController::class, 'invoiceDetails']);
        Route::get('invoices/{id}/download', [PaymentController::class, 'downloadInvoice']);
    });
});
