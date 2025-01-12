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
        $departmentIds = Auth::user()->departments()->pluck('id');

        $this->shifts = Shift::where('start', '>', now()->subDay(1))
            ->whereHas('department', function ($query) use ($departmentIds) {
                $query->whereIn('department_id', $departmentIds);
            })
            ->where('tenant_id', Filament::getTenant()->id)
            ->with([
                'availabilities',
                'shiftSchedules' => function ($query) {
                    $query->where('is_cancelled', false);
                },
                'shiftSchedules.user',
                'shiftSchedules.role',
                'shiftType',
                'department'
            ])
            ->orderBy('start')
            ->get();
    }

    public function render(): View
    {
        return view('livewire.calendar-list', ['shifts' => $this->shifts]);
    }
}
