<?php

namespace App\Livewire;

use App\Models\Availability;
use App\Models\Shift;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use Illuminate\Support\Str;

class AvailabilityList extends Component
{
    public function render()
    {

        $user = Auth::user();
        $userId = $user->id;

        // Haal alle department ID's op van de gebruiker
        $departmentIds = $user->departments->pluck('id');

        // Ophalen van de shifts waar de gebruiker geen beschikbaarheid voor heeft en lid is van de relevante departments
        $shifts = Shift::whereDoesntHave('availabilities', function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
            ->whereIn('department_id', $departmentIds)
            ->orderBy('start')
            ->get();

        return view('livewire.availability-list', ['shifts' => $shifts]);
    }

    public function setAvailability($shiftId, $availability): void
    {
        $userId = Auth::id();
        $tenant = Filament::getTenant();

        // Validatie of shift_id bestaat
        $shift = Shift::find($shiftId);
        if (!$shift) {
            Notification::make()
                ->title('Fout')
                ->body('Onjuiste shift ID.')
                ->danger()
                ->duration(1500)
                ->send();
            return;
        }

        // Controleer of $tenant geen null is
        if (!$tenant) {
            Notification::make()
                ->title('Fout')
                ->body('Ongeldige tenant ID.')
                ->danger()
                ->duration(1500)
                ->send();
            return;
        }

        // Controleren en vaststellen van een geldige UUID voor tenant_id
        $tenantId = $tenant->id;
        if (!Str::isUuid($tenantId)) {
            Notification::make()
                ->title('Fout')
                ->body('Ongeldige tenant ID (geen geldige UUID).')
                ->danger()
                ->duration(1500)
                ->send();
            return;
        }

        // Voeg hier dezelfde controle toe voor userID indien nodig

        Availability::firstOrCreate([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'shift_id' => $shiftId,
        ],[
            'available' => $availability
        ]);

        Notification::make()
            ->title('Hoppa!')
            ->body(trans('shifts.success-notification-body'))
            ->success()
            ->duration(1500)
            ->send();

        Cache::forget('availability_data' . $userId);
    }
}
