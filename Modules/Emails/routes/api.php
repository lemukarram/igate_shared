<?php

use Illuminate\Support\Facades\Route;
use Modules\Emails\Http\Controllers\EmailsController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('emails', EmailsController::class)->names('emails');
});
