<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class MyPayments extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.my-payments';

    public static function getNavigationLabel(): string
    {
        return 'Mijn Vergoedingen';
    }

    public function getHeading(): string | Htmlable
    {
        return 'Mijn Vergoedingen';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Financieel';
    }
}
