<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\SimplePage;
use Illuminate\Contracts\Support\Htmlable;

class UserHasNoTenantSimplePage extends SimplePage
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.auth.user-has-no-tenant-simple-page';

    public function getTitle(): string | Htmlable
    {
        return __('Niet geactiveerd.');
    }

    public function getHeading(): string | Htmlable
    {
        return __('Uw account is niet geactiveerd.');
    }
}
