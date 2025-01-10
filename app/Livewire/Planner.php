<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Shift;
use Filament\Facades\Filament;
use Livewire\Component;
use Carbon\Carbon;

class Planner extends Component
{
    public $currentDate;
    public $departments;
    public $shifts;
    public $tenant;

    public function mount()
    {
        $this->tenant = Filament::getTenant();
        $this->currentDate = Carbon::now();
        $this->loadData();
    }

    public function loadData()
    {
        $this->departments = Department::where('tenant_id', $this->tenant->id)->get();
        $this->shifts = Shift::with(['shiftschedules.user', 'shiftschedules.role', 'shiftType'])->where('tenant_id', $this->tenant->id)->get();
    }

    public function previousWeek()
    {
        $this->currentDate = $this->currentDate->subWeek();
        $this->loadData();
    }

    public function nextWeek()
    {
        $this->currentDate = $this->currentDate->addWeek();
        $this->loadData();
    }

    public function backToToday()
    {
        $this->currentDate = Carbon::now();
        $this->loadData();
    }

    public function getCurrentWeekProperty()
    {
        return $this->currentDate->weekOfYear;
    }

    public function getCurrentMonthProperty()
    {
        return $this->currentDate->month;
    }

    public function getCurrentYearProperty()
    {
        return $this->currentDate->year;
    }

    public function render()
    {
        return view('livewire.planner');
    }
}
