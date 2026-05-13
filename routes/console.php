<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Sync Tap Transaction statuses (useful for failed webhooks or localhost)
Schedule::command('tap:sync-status')->everyFifteenMinutes();

// Automatically capture authorized funds after the configured delay
Schedule::command('tap:auto-capture')->daily();
