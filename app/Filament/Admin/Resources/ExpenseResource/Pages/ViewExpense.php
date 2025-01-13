<?php

namespace App\Filament\Admin\Resources\ExpenseResource\Pages;

use App\Filament\Admin\Resources\ExpenseResource;
use App\Models\Expense;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
//use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Auth;

class ViewExpense extends ViewRecord
{
    protected static string $resource = ExpenseResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('owner.name')
                    ->label('Indiener'),

                TextEntry::make('created_at')
                    ->date('d-m-Y H:i:s')
                    ->label('Indien datum'),

                TextEntry::make('date_expense')
                    ->date('d-m-Y')
                    ->label('Declaratie Datum'),

                TextEntry::make('description')
                    ->label('Beschrijving'),

                TextEntry::make('amount')
                    ->label('Bedrag')
                    ->money('eur'),

                ViewEntry::make('attachments')
                    ->label('Bijlagen')
                    ->view('infolists.components.get-attachments'),

                TextEntry::make('approver.name')
                    ->label('Beoordelaar'),

                TextEntry::make('check_remarks')
                    ->label('Opmerkingen'),

                TextEntry::make('checked_at')
                    ->date('d-m-Y')
                    ->label('Datum beoordeling'),

                IconEntry::make('status')
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
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('approveExpense')
                ->label('Goedkeuren')
                ->color('success')
                ->form([
                    Textarea::make('check_remarks')
                ])
                ->action(function (array $data, Expense $record): void {
                    $record->checked_by = Auth::id();
                    $record->checked_at = now();
                    $record->status = 'approved';
                    $record->check_remarks = $data['check_remarks'];
                    $record->save();
                })
                ->hidden(fn (Expense $record) => $record->status !== 'pending'),

            Action::make('denyExpense')
                ->label('Afkeuren')
                ->color('danger')
                ->form([
                    Textarea::make('check_remarks')
                ])
                ->action(function (array $data, Expense $record): void {
                    $record->checked_by = Auth::id();
                    $record->checked_at = now();
                    $record->status = 'denied';
                    $record->check_remarks = $data['check_remarks'];
                    $record->save();
                })
                ->hidden(fn (Expense $record) => $record->status !== 'pending'),
        ];
    }
}
