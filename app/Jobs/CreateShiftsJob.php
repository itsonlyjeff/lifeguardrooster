<?php

namespace App\Jobs;

use App\Models\Shift;
use App\Models\ShiftSchedule;
use App\Models\ShiftTemplate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Carbon;

class CreateShiftsJob implements ShouldQueue
{
    protected $year;
    protected $months;
    protected $days;
    protected $name;
    protected $startTime;
    protected $endTime;
    protected $template;
    protected $tenant;
    protected $department;
    protected $startScheduling;
    protected $type;

    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct($year, $months, $days, $startTime, $endTime, $name, $template, $type, $department, $tenant, $startScheduling)
    {
        $this->year = $year;
        $this->months = explode(',', $months);
        $this->days = array_map('intval', explode(',', $days)); // Ensure days are integers
        $this->name = $name;
        $this->startTime = $this->validateTime($startTime) ? $startTime : '09:15';
        $this->endTime = $this->validateTime($endTime) ? $endTime : '18:00';
        $this->template = $template;
        $this->type = $type;
        $this->tenant = $tenant;
        $this->department = $department;
        $this->startScheduling = $startScheduling;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $year = $this->year ?: Carbon::now()->year;

        foreach ($this->months as $month) {
            $month = str_pad($month, 2, '0', STR_PAD_LEFT);

            // Determine the last day of this month
            $lastDay = Carbon::parse("{$year}-{$month}-01")->endOfMonth()->day;

            // Iterate through days
            for ($d = 1; $d <= $lastDay; $d++) {
                $day = str_pad($d, 2, '0', STR_PAD_LEFT);
                $date = Carbon::parse("{$year}-{$month}-{$day}");

                // Check if date matches selected days
                if (in_array($date->dayOfWeek, $this->days)) {
                    $this->seedShift($date);
                }
            }
        }
    }

    private function seedShift($date): void
    {
        list($startHour, $startMinute) = explode(':', $this->startTime);
        list($endHour, $endMinute) = explode(':', $this->endTime);

        $start = clone $date;
        $start->setTime($startHour, $startMinute, 0);

        $end = clone $date;
        $end->setTime($endHour, $endMinute, 0);

        if($this->startScheduling != 'no')
        {
            $startScheduling = clone $start;
            $startScheduling->modify('-'.$this->startScheduling.' months');
        } else {
            $startScheduling = null;
        }

        $shift = Shift::create([
            'tenant_id' => $this->tenant,
            'department_id' => $this->department,
            'shift_type_id' => $this->type,
            'start' => $start->toDateTimeString(),
            'end' => $end->toDateTimeString(),
            'start_scheduling' => $startScheduling,
            'name' => $this->name,
        ]);

        $template = ShiftTemplate::findOrFail($this->template);

        foreach ($template->shiftTemplateSchedules as $shiftTemplateSchedule) {
            ShiftSchedule::create([
                'shift_id' => $shift->id,
                'role_id' => $shiftTemplateSchedule->role_id,
                'amount' => $shiftTemplateSchedule->amount,
            ]);
        }
    }

    private function validateTime($time): bool
    {
        $parts = explode(':', $time);
        if (count($parts) === 2) {
            $hour = (int) $parts[0];
            $minute = (int) $parts[1];
            return ($hour >= 0 && $hour <= 23) && ($minute >= 0 && $minute <= 59);
        }
        return false;
    }
}
