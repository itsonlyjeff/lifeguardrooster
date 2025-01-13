<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ExpenseResource\Pages;
use App\Filament\Admin\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Instellingen';
    protected static ?string $navigationLabel = 'Declaraties beoordelen';

    protected static ?string $pluralLabel = "Declaraties beoordelen";


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('owner.name')
                    ->label('Indiener')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('date_expense')
                    ->date('d-m-Y')
                    ->label('Datum')
                    ->sortable(),
                TextColumn::make('description')
                    ->label('Beschrijving'),
                TextColumn::make('amount')
                    ->label('Bedrag')
                    ->money('eur')
                    ->sortable(),
                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'pending' => 'heroicon-o-clock',
                        'approved' => 'heroicon-o-check-circle',
                        'denied' => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'info',
                        'denied' => 'danger',
                        'approved' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('approver.name')
                    ->label("Beoordelaar")
                    ->sortable(),
                TextColumn::make('check_remarks')
                    ->label('Opmerkingen'),
                TextColumn::make('checked_at')
                    ->date('d-m-Y H:i')
                    ->label('Datum beoordeling')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Ter beoordeling',
                        'approved' => 'goedgekeurd',
                        'denied' => 'Afgekeurd',
                    ])
                    ->default('pending')
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = Expense::where('status', 'pending')->count();

        return $count > 0 ? (string) $count : null;
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
            'index' => Pages\ListExpenses::route('/'),
            'view' => Pages\ViewExpense::route('/{record}'),
        ];
    }
}
