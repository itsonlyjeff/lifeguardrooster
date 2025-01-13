<?php

namespace App\Filament\Resources\ExpenseResource\Pages;

use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListExpenses extends ListRecords
{
    protected static string $resource = ExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Declaratie aanmaken'),
            Actions\Action::make('Kilometer Declaratie aanmaken')
                ->form([
                    DatePicker::make('date_expense')->label('Datum')->required(),
                    Textarea::make('description')->label('Reisdoel')->required(),
                    TextInput::make('from')->label('Postcode vertrek')->required(),
                    TextInput::make('to')->label('Postcode aankomst')->required(),
                    TextInput::make('kms')->label('Kilometers')->hint('Kortste route: routenet.nl')->required()->numeric(),
                    Toggle::make('trailer')->label('Met trailer?')
                ])
                ->action(function (array $data) {
                    list($amount, $description) = $this->calculateAmount($data);
                    $this->createExpense($data, $amount, $description);
                })];
    }

    private function calculateAmount(array $data): array
    {
        if($data['trailer']) {
            $amount = $data['kms'] * 0.23;
            $toevoeging = 'met';
        } else {
            $amount = $data['kms'] * 0.19;
            $toevoeging = 'zonder';
        }

        $description = 'Kilometerdeclaratie van '.$data['from'].' naar '.$data['to'].' '.$toevoeging.' trailer ('.$data['kms'].' km). Het opgegeven reisdoel is: '.$data['description'];

        return [$amount, $description];
    }

    private function createExpense(array $data, float $amount, string $description): void
    {
        Expense::create([
            'user_id' => Auth::id(),
            'tenant_id' => Filament::getTenant()->id,
            'date_expense' => $data['date_expense'],
            'description' => $description,
            'amount' => $amount,
            'status' => 'pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Notification::make()->title('Declaratie aangemaakt.')->success()->send();
    }
}
