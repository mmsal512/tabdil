<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

Schedule::command('support:sync-n8n')->everyMinute();

// Visitor Analytics Scheduling
// Check every minute if it's time to send visitor report (based on admin settings)
Schedule::command('visitors:send-report')->everyMinute();

// Check for traffic anomalies every 15 minutes
Schedule::command('visitors:check-alerts')->everyFifteenMinutes();
