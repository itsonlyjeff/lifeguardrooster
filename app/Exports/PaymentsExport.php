<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PaymentsExport implements FromQuery, WithHeadings, WithColumnFormatting, WithStyles
{
    use Exportable;

    private $from;
    private $to;

    public function __construct($from, $to)
    {
        $this->from = Carbon::parse($from)->startOfDay();
        $this->to = Carbon::parse($to)->endOfDay();
    }

    public function query()
    {
        return User::query()
            ->join('shift_schedules', 'users.id', '=', 'shift_schedules.user_id')
            ->join('shifts', 'shift_schedules.shift_id', '=', 'shifts.id')
            ->join('roles', 'shift_schedules.role_id', '=', 'roles.id')
            ->whereBetween('shifts.start', [$this->from, $this->to])
            ->where('shift_schedules.amount', '>', 0)
            ->select([
                'users.name',
                'users.iban',
                'users.iban_tnv',
                DB::raw('DATE_FORMAT(shifts.start, "%d-%m-%Y") as Datum'),
                'roles.name as role_name',
                DB::raw('shift_schedules.amount/100 as Bedrag'),
            ])
            ->orderBy('shifts.start');
    }

    public function headings(): array
    {
        return [
            'Naam',
            'IBAN',
            'IBAN TNV',
            'Datum',
            'Functie',
            'Bedrag'
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => NumberFormat::FORMAT_CURRENCY_EUR,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
        ];
    }
}
