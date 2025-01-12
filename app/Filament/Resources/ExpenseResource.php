<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = "Mijn Declaraties";
    protected static ?string $navigationGroup = "Financieel";
    protected static ?string $pluralLabel = "Declaraties";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('date_expense')
                    ->label('Datum')
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->label('Beschrijving')
                    ->required(),
                Forms\Components\TextInput::make('amount')
                    ->label("Vergoeding")
                    ->default(0)
                    ->minValue(1)
                    ->numeric()
                    ->prefix('€')
                    ->required(),
                SpatieMediaLibraryFileUpload::make('attachment')
                    ->disk('expenses')
                    ->directory('expenses')
                    ->label('Bijlage (pdf, jpg, jpeg, png)')
                    ->nullable()
                    ->collection('expenses')
                    ->multiple()
                    ->reorderable()
                    ->maxSize('1024')
                    ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/jpg', 'image/png']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date_expense')
                    ->date('d-m-Y')
                    ->label('Datum'),
                TextColumn::make('description')
                    ->label('Beschrijving'),
                TextColumn::make('amount')
                    ->label('Bedrag')
                    ->money('eur'),
                IconColumn::make('status')
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
                    }),
                TextColumn::make('approver.name')
                    ->label("Beoordelaar"),
                TextColumn::make('check_remarks')
                    ->label('Opmerkingen'),
                TextColumn::make('checked_at')
                    ->date('d-m-Y H:i')
                    ->label('Datum beoordeling'),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', '=', auth()->id());
    }
}
