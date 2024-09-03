<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Run the command every first day of the month
Schedule::command('app:batch-orders')
    ->monthlyOn(1, '01:00');
