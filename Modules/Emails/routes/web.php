<?php

use Illuminate\Support\Facades\Route;
use Modules\Emails\Http\Controllers\EmailsController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('emails', EmailsController::class)->names('emails');
});
