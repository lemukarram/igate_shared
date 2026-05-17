<?php

use Illuminate\Support\Facades\Route;
use Modules\ClientAPI\Http\Controllers\ClientAPIController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('clientapis', ClientAPIController::class)->names('clientapi');
});
