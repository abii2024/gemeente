<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule data retention purge (GDPR compliance)
Schedule::command('complaints:purge')
    ->daily()
    ->at('02:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/data-retention.log'));

// Schedule overdue complaints check
Schedule::command('complaints:check-overdue')
    ->daily()
    ->at('08:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/overdue-checks.log'));

// Schedule command to clean up temporary uploads
Schedule::command('uploads:clean-temp')
    ->daily()
    ->at('03:30')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/upload-cleanup.log'));
