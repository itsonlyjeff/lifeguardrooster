<?php

namespace App\Filament\Admin\Resources\ShiftResource\RelationManagers;

use App\Models\Role;
use App\Models\ShiftSchedule;
use App\Models\User;
use App\Notifications\UserIsDeletedFromShiftNotification;
use App\Notifications\UserIsPlannedNotification;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification as FilamentNotification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Notification;

class ShiftSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'shiftSchedules';
    protected static ?string $title = 'Rollen';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('role_id')
                    ->label('Rol')
                    ->options(Role::where('tenant_id', Filament::getTenant()->id)->pluck('name', 'id'))
                    ->required(),
                Select::make('user_id') // change to match column name of user foreign key in your table
                ->options(User::orderBy('name')
                    ->pluck('name', 'id')
                    ->toArray()),
                TextInput::make('amount')
                    ->label("Vergoeding")
                    ->default(0)
                    ->numeric()
                    ->prefix('€'),
                Textarea::make('remarks')
                    ->label('Opmerkingen'),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('is_cancelled', false))
            ->columns([
                Tables\Columns\TextColumn::make('role.name')
                    ->label('Rol'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Naam'),
                Tables\Columns\TextColumn::make('amount')
                    ->label("Vergoeding")
                    ->money('eur'),
                Tables\Columns\TextColumn::make('remarks')
                    ->label('Opmerkingen'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('Stuur mail')
                    ->hidden(fn (Shiftschedule $record): bool => !$record->user()->exists() || !is_null($record->notification_at))
                    ->icon('heroicon-o-envelope')
                    ->color('success')
                    ->action(function (Shiftschedule $record) {
                        Notification::send($record->user, new UserIsPlannedNotification($record, $record->shift, $record->user));

                        $record->notification_at = now();
                        $record->save();

                        FilamentNotification::make()
                            ->title('Mail is succesvol verstuurd.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('Uitschrijven')
                    ->icon('heroicon-o-user-minus')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function ($record) {
                        $record->is_cancelled = true;
                        $record->save();

                        ShiftSchedule::create([
                            'shift_id' => $record->shift_id,
                            'role_id' => $record->role_id,
                            'amount' => $record->amount,
                        ]);

                        $availability = $record->user->availabilities()->where('shift_id', $record->shift_id)->first();
                        $availability->available = false;
                        $availability->save();

                        Notification::send($record->user, new UserIsDeletedFromShiftNotification($record, $record->shift, $record->user));
                        $record->notification_at = now();
                        $record->save();

                        FilamentNotification::make()
                            ->title('Hoppa!')
                            ->body('Gebruiker succesvol uitgeschreven voor deze dienst.')
                            ->success()
                            ->duration(1500)
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
