<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Planner extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.planner';

    protected static ?string $navigationGroup = 'Rooster';

}