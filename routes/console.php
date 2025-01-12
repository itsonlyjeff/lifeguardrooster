<?php

use App\Jobs\AutoScheduleJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Spatie\Health\Commands\RunHealthChecksCommand;
use ShuvroRoy\FilamentSpatieLaravelHealth\Pages\HealthCheckResults as BaseHealthCheckResults;

// Rooster
Schedule::command('availability:unset')->everyMinute();
Schedule::job(new AutoScheduleJob)->everyMinute();

Schedule::command(RunHealthChecksCommand::class)->everyMinute();
Schedule::command('telescope:prune --hours=48')->daily();


