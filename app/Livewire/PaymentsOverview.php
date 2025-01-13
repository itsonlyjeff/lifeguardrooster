<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\ShiftSchedule;
use App\Models\User;
use Livewire\Component;

class PaymentsOverview extends Component
{
    public $selectedYear;
    public $selectedMonth;

    private const MONTHS_IN_YEAR = ['all', 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

    public function mount(): void
    {
        $this->selectedYear = date('Y');
        $this->selectedMonth = date('n');
    }

    public function getYearsProperty(): array
    {
        [$earliestYearShift, $latestYearShift] = $this->getEarliestAndLatestYearForShifts();
        $currentYear = date('Y');

        if (!$earliestYearShift || !$latestYearShift) {
            $earliestYear = $latestYear = $currentYear;
        } else {
            $earliestYear = max($earliestYearShift, 2000); // At least year 2000
            $latestYear = min($latestYearShift, $currentYear); // At most current year
        }

        return range($earliestYear, $latestYear);
    }

    private function getEarliestAndLatestYearForShifts(): array
    {
        $earliestShiftStart = ShiftSchedule::join('shifts', 'shift_schedules.shift_id', '=', 'shifts.id')
            ->min('shifts.start');
        $latestShiftStart = ShiftSchedule::join('shifts', 'shift_schedules.shift_id', '=', 'shifts.id')
            ->max('shifts.start');

        $earliestYearShift = $earliestShiftStart ? (int) date('Y', strtotime($earliestShiftStart)) : null;
        $latestYearShift = $latestShiftStart ? (int) date('Y', strtotime($latestShiftStart)) : null;

        return [$earliestYearShift, $latestYearShift];
    }

    public function getMonthsProperty(): array
    {
        return ['all'] + range(0, 12);
    }

    public function getUsersProperty()
    {
        $usersQuery = User::query()
            ->with(['shiftSchedules.role'])
            ->select('users.*')
            ->whereHas('shiftSchedules', function ($query) {
                $query->join('shifts', 'shift_schedules.shift_id', '=', 'shifts.id')
                    ->whereYear('shifts.start', $this->selectedYear)
                    ->where('shift_schedules.amount', '>', 0);

                if ($this->selectedMonth !== 'all') {
                    $query->whereMonth('shifts.start', $this->selectedMonth);
                }
            })
            ->with(['shiftSchedules' => function ($query) {
                $query->join('shifts', 'shift_schedules.shift_id', '=', 'shifts.id')
                    ->whereYear('shifts.start', $this->selectedYear)
                    ->where('shift_schedules.amount', '>', 0);

                if ($this->selectedMonth !== 'all') {
                    $query->whereMonth('shifts.start', $this->selectedMonth);
                }
            }])
            ->orderBy('users.name');

        return $usersQuery->get();
    }

    public function render()
    {
        $shiftSchedulesQuery = ShiftSchedule::where('amount', '>', 0) // where amount is greater than 0, exlcude 0 values
        ->join('shifts', 'shift_schedules.shift_id', '=', 'shifts.id') // join the shifts table
        ->whereYear('shifts.start', $this->selectedYear); // only get shifts from the selected year

        if ($this->selectedMonth !== 'all') { // if a specific month is selected
            $shiftSchedulesQuery->whereMonth('shifts.start', $this->selectedMonth); // only get shifts from the selected month
        }

        $shiftSchedules = $shiftSchedulesQuery->get(); // get the results

        $usersWithPositiveAmount = $this->users;

        return view('livewire.payments-overview', [
            'shiftSchedules' => $shiftSchedules,
            'years' => $this->years,
            'months' => $this->months,
            'usersWithPositiveAmount' => $usersWithPositiveAmount,
        ]);
    }
}
