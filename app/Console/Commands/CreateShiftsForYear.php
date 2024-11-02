<?php

namespace App\Console\Commands;

use App\Jobs\CreateShiftsJob;
use App\Models\Department;
use App\Models\ShiftTemplate;
use App\Models\ShiftType;
use App\Models\Tenant;
use Illuminate\Console\Command;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\multiselect;

class CreateShiftsForYear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shifts:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all shifts for the given year.';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = select(
            label: 'Tenant',
            options: Tenant::all()->pluck('name', 'id')->toArray(),
            required: true,
        );

        $department = select(
            label: 'Department',
            options: Department::where('tenant_id', $tenant)->pluck('name', 'id')->toArray(),
            required: true,
        );

        $type = select(
            label: 'Shift Type',
            options: ShiftType::where('tenant_id', $tenant)->pluck('name', 'id')->toArray(),
            required: true,
        );

        $template = select(
            label: 'Template',
            options: ShiftTemplate::where('tenant_id', $tenant)->pluck('name', 'id')->toArray(),
            required: true,
        );

        $startScheduling = select(
            label: 'Start Scheduling?',
            options: [
                'no' => 'Nee',
                '1' => '1 Maand',
                '2' => '2 Maand',
                '3' => '3 Maand',
                '4' => '4 Maand',
            ],
            required: true,
        );

        $year = text(
            label: 'Year',
            default: date("Y"),
            required: true,
        );

        $name = text(
            label: 'Name',
            default: 'Bewaking Quackstrand',
            required: true,
        );

        $months = multiselect(
            label: 'Months',
            options: [
                '1' => 'January',
                '2' => 'February',
                '3' => 'March',
                '4' => 'April',
                '5' => 'May',
                '6' => 'June',
                '7' => 'July',
                '8' => 'August',
                '9' => 'September',
                '10' => 'October',
                '11' => 'November',
                '12' => 'December',
            ],
            required: true,
        );

        $days = multiselect(
            label: 'Days',
            options: [
                '1' => 'Monday',
                '2' => 'Tuesday',
                '3' => 'Wednesday',
                '4' => 'Thursday',
                '5' => 'Friday',
                '6' => 'Saturday',
                '0' => 'Sunday',
            ],
            required: true,
        );

        $startTime = text(
            label: 'Start Time (HH:MM)',
            default: '09:15',
            required: true,
        );

        $endTime = text(
            label: 'End Time (HH:MM)',
            default: '18:00',
            required: true,
        );

        CreateShiftsJob::dispatch($year, implode(',', $months), implode(',', $days), $startTime, $endTime, $name, $template, $type, $department, $tenant, $startScheduling);

        $this->info('Shifts creation job dispatched successfully.');
    }
}
