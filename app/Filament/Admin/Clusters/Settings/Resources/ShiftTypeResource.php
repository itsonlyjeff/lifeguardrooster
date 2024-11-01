<?php

namespace App\Filament\Admin\Clusters\Settings\Resources;

use App\Filament\Admin\Clusters\Settings;
use App\Filament\Admin\Resources\ShiftTypeResource\Pages;
use App\Filament\Admin\Resources\ShiftTypeResource\RelationManagers;
use App\Models\ShiftType;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShiftTypeResource extends Resource
{
    protected static ?string $model = ShiftType::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Settings::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                ColorPicker::make('bg_color')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                ColorColumn::make('bg_color'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Clusters\Settings\Resources\ShiftTypeResource\Pages\ListShiftTypes::route('/'),
            'create' => \App\Filament\Admin\Clusters\Settings\Resources\ShiftTypeResource\Pages\CreateShiftType::route('/create'),
            'edit' => \App\Filament\Admin\Clusters\Settings\Resources\ShiftTypeResource\Pages\EditShiftType::route('/{record}/edit'),
        ];
    }
}
