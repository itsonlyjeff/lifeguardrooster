<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\UserResource\Pages;

use App\Filament\Admin\Clusters\Settings\Resources\UserResource;
use App\Http\Middleware\ApplyTenantScopes;
use App\Models\User;
use Exception;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Gebruiker toevoegen')
                ->form([
                    Select::make('user')
                        ->label('Gebruikers')
                        ->options(
                            User::withoutGlobalScopes()
                                ->whereDoesntHave('tenants', function ($query) {
                                    $query->where('tenants.id', Filament::getTenant()->id);
                                })
                                ->orderBy('name')
                                ->pluck('name', 'id')
                        )
                        ->searchable()
                        ->multiple()
                        ->preload(true)
                        ->required(),

                    Toggle::make('active')
                        ->label('Direct actief maken?')
                ])
            ->action(function (array $data) {
                $tenant = Filament::getTenant();

                if (!$tenant) {
                    throw new Exception('Geen tenant geselecteerd.');
                }

                foreach ($data['user'] as $userId) {
                    $tenant->users()->attach($userId, [
                        'is_active' => $data['active'] ?? false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }

                Notification::make()
                    ->title('Gelukt!')
                    ->body('Gebruikers zijn toegevoegd.')
                    ->success()
                    ->send();
            }),
        ];
    }
}
