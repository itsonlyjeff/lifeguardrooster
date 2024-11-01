<?php

namespace App\Filament\Admin\Clusters\Settings\Resources\ShiftTemplateResource\RelationManagers;

use App\Models\Role;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ShiftTemplateSchedulesRelationManager extends RelationManager
{
    protected static string $relationship = 'shiftTemplateSchedules';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('role_id')
                    ->label('Rol')
                    ->options(Role::where('tenant_id', Filament::getTenant()->id)->pluck('name', 'id'))
                    ->required(),
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
