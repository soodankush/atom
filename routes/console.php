<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('schedule-tasks', function (Schedule $schedule) {
    $schedule->command('app:update-not-returned-rentals-as-over-due')->dailyAt('00:00');
    $schedule->command('send:overdue-emails-to-users')->dailyAt('00:30');
})->describe('Schedules tasks to run and update the status of overdue rentals and send notifications to the user via email');
