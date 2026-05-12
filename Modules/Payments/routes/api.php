<?php

use Illuminate\Support\Facades\Route;
use Modules\Payments\Http\Controllers\PaymentApiController;
use Modules\Payments\Http\Controllers\TapWebhookController;

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

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/payments/checkout', [PaymentApiController::class, 'checkout']);
});

// Mobile callback doesn't strictly need auth here.
Route::get('/payments/mobile-callback', function () {
    return response()->json(['message' => 'Payment verifying... Please wait for webhook processing.']);
});

// Webhook endpoint (must be unprotected by auth middleware)
Route::post('/payments/webhook/tap', [TapWebhookController::class, 'handle'])->name('payments.webhook.tap');
