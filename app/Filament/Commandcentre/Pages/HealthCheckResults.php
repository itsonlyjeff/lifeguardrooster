<?php

namespace App\Filament\Commandcentre\Pages;

use Filament\Pages\Page;
use ShuvroRoy\FilamentSpatieLaravelHealth\Pages\HealthCheckResults as BaseHealthCheckResults;
use Illuminate\Contracts\Support\Htmlable;

class HealthCheckResults extends BaseHealthCheckResults
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    public function getHeading(): string | Htmlable
    {
        return 'HealthChecks';
    }

    public static function getNavigationLabel(): string
    {
        return 'HealthChecks';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Monitoring';
    }

}
