<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('queue:work --timeout=600')->everyMinute();