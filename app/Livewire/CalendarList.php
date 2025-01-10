<?php

namespace App\Livewire;

use App\Models\Shift;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\View\View;


class CalendarList extends Component
{
    public bool $userOnly = false;
    public $shifts;

    public function mount(): void
    {
        $this->shifts = Shift::where('start', '>', now()->subDay(1))
            ->whereHas('department', function ($query) {
                $query->whereIn('department_id', Auth::user()->departments()->pluck('id'));
            })
            ->where('tenant_id', Filament::getTenant()->id)
            ->with(['availabilities', 'shiftschedules', 'shiftschedules.user', 'shiftschedules.role', 'shiftType', 'department'])
            ->orderBy('start')
            ->get();
    }
    public function render(): View
    {
        return view('livewire.calendar-list', ['shifts' => $this->shifts]);
    }
}
