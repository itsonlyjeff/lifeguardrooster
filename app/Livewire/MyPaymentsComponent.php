<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\ShiftSchedule;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class MyPaymentsComponent extends Component
{
    public $selectedYear;

    public function mount(): void
    {
        $this->selectedYear = date('Y');
    }

//    public function updatedSelectedYear($year)
//    {
//        // This method will be called when $selectedYear is updated.
//        $this->selectedYear = $year;
//        info('Year Updated. New Year: '.$year);
//    }

    public function getYearsProperty(): array
    {
        $earliestYearShift = ShiftSchedule::where('user_id', Auth::id())
            ->with('shift') // Gebruik de relatie hier
            ->get()
            ->pluck('shift.start') // Ophalen shift startdatums
            ->min();

        $latestYearShift = ShiftSchedule::where('user_id', Auth::id())
            ->with('shift') // Zorg ervoor dat de relatie shift is geladen
            ->get()
            ->pluck('shift.start')
            ->max();

        $earliestYearExpense = Expense::where('user_id', Auth::id())
            ->min('created_at');

        $latestYearExpense = Expense::where('user_id', Auth::id())
            ->max('created_at');

        // Extract the year from the date and convert it to an integer
        if(!$earliestYearShift || !$latestYearShift || !$earliestYearExpense || !$latestYearExpense){
            $earliestYear = $latestYear = date("Y");
        } else {
            $earliestYear = min((int) date('Y', strtotime($earliestYearShift)), (int) date('Y', strtotime($earliestYearExpense)));
            $latestYear = max((int) date('Y', strtotime($latestYearShift)), (int) date('Y', strtotime($latestYearExpense)));
        }

        return range($earliestYear, $latestYear);
    }

    public function render(): view
    {
        $shiftschedules = ShiftSchedule::with('shift')
            ->where('user_id', Auth::id())
            ->where('amount', '>', 0)
            ->whereHas('shift', function ($query) {
                $query->whereDate('start', '<', now())
                    ->whereYear('start', $this->selectedYear);
            })
            ->get();

        $total_amount_shiftschedules = $shiftschedules->sum('amount');

        $expenses = Expense::where('user_id', Auth::id())
            ->where('status', 'approved')
            ->whereYear('created_at',  $this->selectedYear)
            ->get();

        $total_amount_expenses = $expenses->sum('amount');

        return view('livewire.my-payments-component', [
            'shiftschedules' => $shiftschedules,
            'total_amount_shiftschedules' => $total_amount_shiftschedules,
            'years' => $this->years,
            'expenses' => $expenses,
            'total_amount_expenses' => $total_amount_expenses,
        ]);
    }
}
