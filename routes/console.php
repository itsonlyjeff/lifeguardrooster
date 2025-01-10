<?php

use App\Jobs\AutoScheduleJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

// Rooster
Schedule::command('availability:unset')->everyMinute();
Schedule::job(new AutoScheduleJob)->everyMinute();
