<?php

namespace App\Filament\Admin\Pages;

use App\Exports\PaymentsExport;
use App\Models\User;
use Digitick\Sepa\DomBuilder\DomBuilderFactory;
use Digitick\Sepa\GroupHeader;
use Digitick\Sepa\PaymentInformation;
use Digitick\Sepa\TransferFile\CustomerCreditTransferFile;
use Digitick\Sepa\TransferInformation\CustomerCreditTransferInformation;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Auth;
use Digitick\Sepa\TransferFile\Factory\TransferFileFacadeFactory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class PaymentsOverview extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.admin.pages.payments-overview';

    public static function getNavigationLabel(): string
    {
        return 'Financieel overzicht';
    }

    public function getHeading(): string|Htmlable
    {
        return 'Financieel overzicht';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Penningmeester';
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('Excel Export')
                ->form([
                    DatePicker::make('from')
                        ->label('Vanaf wanneer?')
                        ->required(),
                    DatePicker::make('to')
                        ->label('Tot wanneer?')
                        ->required(),
                ])
                ->action(function (array $data) {
                    return (new PaymentsExport($data['from'], $data['to']))->download('payments.xlsx');
                }),

            Action::make('Betaalbestand')
                ->form([
                    DatePicker::make('from')
                        ->label('Vanaf wanneer?')
                        ->required(),
                    DatePicker::make('to')
                        ->label('Tot wanneer?')
                        ->required(),
                    TextInput::make('description')
                        ->label('Omschrijving')
                        ->required(),
                ])
                ->action(function (array $data) {

                    return $this->creditTransferFile($data['from'], $data['to'], $data['description']);
                })
        ];
    }

    public function creditTransferFile($from, $to, $description)
    {
        $users = User::query()
            ->join('shift_schedules', 'users.id', '=', 'shift_schedules.user_id')
            ->join('shifts', 'shift_schedules.shift_id', '=', 'shifts.id')
            ->join('roles', 'shift_schedules.role_id', '=', 'roles.id')
            ->whereBetween('shifts.start', [$from, $to])
            ->where('shift_schedules.amount', '>', 0)
            ->whereNotNull('users.iban')
            ->select([
                'users.name',
                'users.iban',
                DB::raw('SUM(shift_schedules.amount) as TotalBedrag'),
            ])
            ->groupBy('users.id')
            ->get();

        $uniqueMessageIdentification = 'SSAVW-' . date('Ymd') . '-' . rand(1000, 9999);
        $customerCredit = TransferFileFacadeFactory::createCustomerCredit($uniqueMessageIdentification, 'Stichting Stad aan veilig Water');

        $paymentId = 'Payment-' . date('Ymd') . '-001'; // Payment-20240913-001
        $customerCredit->addPaymentInfo($paymentId, array(
            'id' => $paymentId,
            'debtorName' => 'Stichting Stad aan Veilig Water',
            'debtorAccountIBAN' => 'NL55RBRB0706373219',
            'debtorAgentBIC' => 'RBRBNL21',
            'batchBooking' => true,
        ));

        foreach ($users as $user) {
            $iban = $user->iban;
            $bic = Http::get('https://openiban.com/validate/' . $iban . '?getBIC=true');
            $bic = $bic->json()['bankData']['bic'];

            $customerCredit->addTransfer($paymentId, array(
                'amount' => $user->TotalBedrag, // `amount` should be in cents
                'creditorIban' => $iban,
                'creditorBic' => $bic,
                'creditorName' => $user->name,
                'remittanceInformation' => $description,
            ));
        }
        $xmlContent = $customerCredit->asXML();

        // Define file path
        $filePath = storage_path('app/public/sepa.xml');

        // Save content to the file
        file_put_contents($filePath, $xmlContent);

        // Create a response that forces a download
        return response()->download($filePath, $uniqueMessageIdentification . '-sepa.xml')->deleteFileAfterSend();
    }
}
