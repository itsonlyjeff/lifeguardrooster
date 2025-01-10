<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CalendarList extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static string $view = 'filament.pages.calendar-list';
    protected static ?string $navigationLabel = 'Rooster Lijst';

    protected static ?string $title = 'Rooster lijst weergave';
    protected static ?string $navigationGroup = 'Rooster';
}
