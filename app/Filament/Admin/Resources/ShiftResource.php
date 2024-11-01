<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ShiftResource\Pages;
use App\Filament\Admin\Resources\ShiftResource\RelationManagers;
use App\Models\Department;
use App\Models\Shift;
use App\Models\ShiftTemplate;
use App\Models\ShiftType;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ShiftResource extends Resource
{
    protected static ?string $model = Shift::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $components = [
            TextInput::make('name')
                ->label('Naam')
                ->required(),
            DateTimePicker::make('start')
                ->label('Aanvang dienst')
                ->seconds(false)
                ->weekStartsOnMonday()
                ->displayFormat('d-m-Y H:i:s')
                ->required(),
            DateTimePicker::make('end')
                ->label('Einde dienst')
                ->seconds(false)
                ->weekStartsOnMonday()
                ->displayFormat('d-m-Y H:i:s')
                ->after('start')
                ->required(),
            DateTimePicker::make('start_scheduling')
                ->label('Automatisch Roosteren vanaf (leeg is manueel roosteren)')
                ->seconds(false)
                ->weekStartsOnMonday()
                ->displayFormat('d-m-Y H:i')
                ->nullable(),
            Select::make('department_id')
                ->label('Afdeling')
                ->required()
                ->options(Department::where('tenant_id', Filament::getTenant()->id)->pluck('name', 'id')),
            Select::make('shift_type_id')
                ->label('Soort dienst')
                ->required()
                ->options(ShiftType::where('tenant_id', Filament::getTenant()->id)->pluck('name', 'id')),

        ];

        return $form
            ->schema($components);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Naam')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('shiftType.name')
                    ->label('Soort')
                    ->sortable()
                    ->badge(),
                TextColumn::make('start')
                    ->label('Aanvang dienst')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('end')
                    ->label('Einde dienst')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('start_scheduling')
                    ->label('Automatisch Roosteren vanaf')
                    ->dateTime('d-m-Y H:i')
                    ->sortable()
                    ->searchable(),
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
            RelationManagers\ShiftSchedulesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListShifts::route('/'),
            'create' => Pages\CreateShift::route('/create'),
            'edit' => Pages\EditShift::route('/{record}/edit'),
        ];
    }
}
