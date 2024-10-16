<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    if (!Str::contains(Shell::exec('ps aux | grep "queue:work"'), 'artisan queue:work')) {
        Artisan::call('queue:work');
    }
})->everyThirtySeconds()->withoutOverlapping();