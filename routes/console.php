<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('subscriptions:check')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->runInBackground();


Schedule::command('fees:generate-demands')
    ->monthlyOn(1, '06:00') // 1st of every month at 6am
    ->withoutOverlapping();