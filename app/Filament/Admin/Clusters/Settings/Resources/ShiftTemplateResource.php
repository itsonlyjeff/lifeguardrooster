<?php

namespace App\Filament\Admin\Clusters\Settings\Resources;

use App\Filament\Admin\Clusters\Settings;
use App\Filament\Admin\Resources\ShiftTemplateResource\Pages;
use App\Filament\Admin\Resources\ShiftTemplateResource\RelationManagers;
use App\Models\ShiftTemplate;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ShiftTemplateResource extends Resource
{
    protected static ?string $model = ShiftTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = Settings::class;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
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
            \App\Filament\Admin\Clusters\Settings\Resources\ShiftTemplateResource\RelationManagers\ShiftTemplateSchedulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Admin\Clusters\Settings\Resources\ShiftTemplateResource\Pages\ListShiftTemplates::route('/'),
            'create' => \App\Filament\Admin\Clusters\Settings\Resources\ShiftTemplateResource\Pages\CreateShiftTemplate::route('/create'),
            'edit' => \App\Filament\Admin\Clusters\Settings\Resources\ShiftTemplateResource\Pages\EditShiftTemplate::route('/{record}/edit'),
        ];
    }
}