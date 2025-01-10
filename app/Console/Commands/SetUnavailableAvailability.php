<?php

namespace App\Console\Commands;

use App\Models\Availability;
use App\Models\Shift;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;

class SetUnavailableAvailability extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'availability:unset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the availability to false for every user which has not set the availability already and for shifts before today';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $pastShifts = Shift::where('start', '<=', now())->get();

        $tenants = Tenant::all();

        foreach ($tenants as $tenant) {
            $users = User::whereRelation('tenants', 'tenant_id', $tenant->id)->get();

            foreach ($users as $user) {
                foreach ($pastShifts as $shift) {
                    // Check if the user has an availability for this shift
                    $availability = $user->availabilities()->where('shift_id', $shift->id)->first();

                    // If no availability was found, create one with 'available' set to false
                    if ($availability === null) {
                        Availability::create([
                            'tenant_id' => $shift->tenant_id,
                            'user_id' => $user->id,
                            'shift_id' => $shift->id,
                            'available' => false,
                            'notes' => 'Door systeem op afwezig gezet door niet opgeven beschikbaarheid.',
                        ]);
                    }
                }
            }

            $this->info('Availability has been set to false successfully for shifts before today and today\'s shifts for users without availabilities.');

        }
        }

}
