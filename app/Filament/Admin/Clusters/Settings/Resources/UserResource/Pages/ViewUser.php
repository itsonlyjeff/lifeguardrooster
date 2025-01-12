<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\UserResource\Pages;

use App\Filament\Admin\Clusters\Settings\Resources\UserResource;
use App\Models\User;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Database\Eloquent\Model;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function mount(string|int $record): void
    {
        parent::mount($record); // Zorgt dat $this->record wordt geladen
        $this->record->load(['tenants', 'roles.tenant', 'departments.tenant']); // Laad de relaties vooraf
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Fieldset::make('Account')->schema([
                    TextEntry::make('name')->label('Naam'),
                    TextEntry::make('email')
                        ->icon('heroicon-m-envelope')
                        ->iconColor('primary')
                        ->copyable()
                        ->copyMessage('Gekopieerd!')
                        ->copyMessageDuration(1500),
                    TextEntry::make('iban_tnv')
                        ->label('Tenaamstelling')
                        ->icon('heroicon-m-currency-euro')
                        ->iconColor('primary'),
                    TextEntry::make('masked_iban')
                        ->label('IBAN')
                        ->icon('heroicon-m-currency-euro')
                        ->iconColor('primary'),
                    TextEntry::make('email_verified_at')->date('d-m-Y H:i:s')->label('Email geverifieerd op'),
                    TextEntry::make('created_at')->date('d-m-Y H:i:s')->label('Account aangemaakt op'),
                    TextEntry::make('updated_at')->date('d-m-Y H:i:s')->label('Account gewijzigd op'),
                    IconEntry::make('is_active')
                        ->boolean()
                        ->label('Actief')
                        ->getStateUsing(function (User $user) {
                            return $user->tenants()->where('tenant_id', \Filament\Facades\Filament::getTenant()->id)->first()->pivot->is_active;
                        }),
                    IconEntry::make('is_admin')
                        ->boolean()
                        ->label('Administrator')
                        ->getStateUsing(function (User $user) {
                            return $user->tenants()->where('tenant_id', \Filament\Facades\Filament::getTenant()->id)->first()->pivot->is_admin;
                        }),
                ]),
                Fieldset::make('Teams')->schema([
                    RepeatableEntry::make('tenants')
                        ->schema([
                            TextEntry::make('name')->label('Naam'),
                        ])->grid(2),
                ])->columnSpanFull(),
                Fieldset::make('Rollen')->schema([
                    RepeatableEntry::make('roles')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('name')->label('Naam'),
                            TextEntry::make('tenant.name')->hiddenLabel()->badge(true),
                        ])->grid(2),
                ])->columnSpanFull(),
                Fieldset::make('Afdelingen')->schema([
                    RepeatableEntry::make('departments')
                        ->hiddenLabel()
                        ->schema([
                            TextEntry::make('name')->label('Naam'),
                            TextEntry::make('tenant.name')->hiddenLabel()->badge(true),
                        ])->grid(2)
                ])->columnSpanFull(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Account deactiveren')
                ->color('danger')
                ->requiresConfirmation()
                ->hidden(function ($record) {
                    $tenant = Filament::getTenant()->id;
                    $user = $record;
                    $pivot = $record->tenants()->where('tenant_id', $tenant)->first()->pivot;

                    return !($pivot->is_active);
                })
                ->action(function ($record) {
                    $tenant = Filament::getTenant()->id;
                    $user = $record;
                    $pivot = $record->tenants()->where('tenant_id', $tenant)->first()->pivot;

                    $pivot->is_active = false;
                    $pivot->save();

                    Notification::make()
                        ->title('Gebruiker is gedeactiveerd')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('Account activeren')
                ->color('success')
                ->requiresConfirmation()
                ->hidden(function ($record) {
                    $tenant = Filament::getTenant()->id;
                    $pivot = $record->tenants()->where('tenant_id', $tenant)->first()->pivot;

                    return $pivot->is_active;
                })
                ->action(function ($record) {
                    $tenant = Filament::getTenant()->id;
                    $pivot = $record->tenants()->where('tenant_id', $tenant)->first()->pivot;

                    $pivot->is_active = true;
                    $pivot->save();

                    Notification::make()
                        ->title('Gebruiker is gedeactiveerd')
                        ->success()
                        ->send();
                }),
        ];
    }
}
