<?php

namespace App\Filament\Admin\Resources\ShiftResource\RelationManagers;

use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShiftSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'shiftSchedules';

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

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('role.name')
                    ->label('Rol'),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Naam'),
                Tables\Columns\TextColumn::make('amount')
                    ->label("Vergoeding")
                    ->money('eur'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
