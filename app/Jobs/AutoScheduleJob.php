<?php

namespace App\Jobs;

use App\Enums\ShiftRolesEnum;
use App\Models\Shift;
use App\Models\User;
use App\Notifications\UserIsPlannedNotification;
use Carbon\Carbon;
use Exception;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class AutoScheduleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $shiftsForToday = Shift::where('start_scheduling', '<=', Carbon::now())
            ->where('start', '>=', Carbon::now())
            ->with(['shiftSchedules.user', 'shiftSchedules.role'])
            ->get();

        foreach ($shiftsForToday as $shift) {
            foreach ($shift->shiftSchedules as $schedule) {
                if ($schedule->user_id === null) {
                    $shiftStart = $shift->start;
                    $shiftEnd = $shift->end;
                    $tenant_id = $shift->tenant_id;

                    $leastBusyUserForRole = $this->getLeastBusyUserForRole($schedule->role->id, $shift->id, $shiftStart, $shiftEnd, $tenant_id);

                    if ($leastBusyUserForRole !== null) {
                        $schedule->user_id = $leastBusyUserForRole->id;
                        $schedule->notification_at = now();
                        $schedule->save();

                        $this->notifyUser($leastBusyUserForRole, $schedule->role->name, $shift->start);
                        $this->mailUser($leastBusyUserForRole, $schedule, $shift);
                    }
                }
            }
        }
    }

    public function getLeastBusyUserForRole($roleId, $shiftId, $shiftStart, $shiftEnd, $tenant_id)
    {
        $user = User::select('users.*')
            ->with('tenants')
            ->whereHas('tenants', function ($query) use ($tenant_id) {
                $query->where('tenants.id', $tenant_id)
                    ->where('tenant_user.is_active', true); // Controleer 'is_active'
            })
            ->whereExists(function ($query) use ($roleId) {
                $query->select(DB::raw(1))
                    ->from('role_user')
                    ->whereColumn('role_user.user_id', 'users.id')
                    ->where('role_user.role_id', $roleId);
            })
            ->whereNotExists(function ($query) use ($shiftId, $shiftStart, $shiftEnd) {
                $query->select(DB::raw(1))
                    ->from('shift_schedules')
                    ->join('shifts', 'shifts.id', '=', 'shift_schedules.shift_id')
                    ->whereColumn('shift_schedules.user_id', 'users.id')
                    ->where(function ($query) use ($shiftStart, $shiftEnd) {
                        $query->where('shifts.start', '<', $shiftEnd)
                            ->where('shifts.end', '>', $shiftStart);
                    });
            })
            ->whereNotExists(function ($query) use ($shiftId) {
                $query->select(DB::raw(1))
                    ->from('shift_schedules')
                    ->whereColumn('shift_schedules.user_id', 'users.id')
                    ->where('shift_schedules.shift_id', $shiftId);
            })
            ->whereHas('availabilities', function ($query) use ($shiftId) {
                $query->where('shift_id', $shiftId)
                    ->where('available', true);
            })
            ->withCount('shiftschedules')
            ->orderBy('shiftschedules_count', 'asc')
            ->first();

        return $user;
    }

    public function notifyUser($user, $role, $day): void
    {
        FilamentNotification::make()
            ->title('Ingeroosterd als '.$role)
            ->body('Datum: '.$day->format('d-m-Y H:i').' uur.')
            ->sendToDatabase($user);
    }

    public function mailUser($user, $schedule, $shift): void
    {
        $shift->load('shiftSchedules.user', 'shiftSchedules.role');
        Notification::send($user, new UserIsPlannedNotification($schedule, $shift, $user));
    }
}
