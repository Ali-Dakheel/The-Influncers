<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('campaigns:remind-deadlines')->dailyAt('09:00');
Schedule::command('campaigns:close-expired')->hourly();
Schedule::command('content:archive')->dailyAt('03:00');
