<?php

namespace App\Filament\Pages;

use App\Models\Shift;
use Filament\Facades\Filament;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class AvailabilityList extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static string $view = 'filament.pages.availability-list';

    protected static ?string $navigationLabel = 'Beschikbaarheid';
    protected static ?string $title = 'Beschikbaarheid';

    public static function getNavigationBadge(): ?string
    {
        $user = Auth::user(); // Haal de momenteel geauthentiseerde gebruiker op
        $userId = $user->id; // Haal de ID van de geauthentiseerde gebruiker op

        // Haal de department ID's op van de gebruiker
        $departmentIds = $user->departments->pluck('id');

        // Tel de shifts waar de gebruiker geen beschikbaarheid voor heeft en lid is van de relevante departments
        $count = Shift::whereDoesntHave('availabilities', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereIn('department_id', $departmentIds)
            ->where('tenant_id', Filament::getTenant()->id)
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
