<?php

use Illuminate\Support\Facades\Route;
use Modules\Payments\Http\Controllers\CheckoutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth'])->group(function () {
    Route::post('/payments/checkout', [CheckoutController::class, 'checkout'])->name('payments.checkout');
});

// The callback doesn't need auth, as it's returning from a 3rd party.
Route::get('/payments/callback', [CheckoutController::class, 'callback'])->name('payments.callback');
