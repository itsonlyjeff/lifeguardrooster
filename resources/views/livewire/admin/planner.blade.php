<div class="flex flex-col">
    <!-- Navigatie voor weken -->
    <div class="flex space-x-1 items-center mb-4">
        <button wire:click="previousWeek"
                class="bg-blue-200 py-2 px-4 font-bold text-blue-500 rounded hover:bg-blue-100">
            &lt;
        </button>

        <span class="bg-blue-200 py-2 px-4 font-bold rounded">
             Wk {{ $this->currentWeek }}, {{ \Carbon\Carbon::create($this->currentYear, $this->currentMonth)->format('M') }} {{ $this->currentYear }}
        </span>

        <button wire:click="backToToday"
                class="bg-blue-200 py-2 px-4 font-bold text-blue-500 rounded hover:bg-blue-100">
            <x-css-today/>
        </button>

        <button wire:click="nextWeek" class="bg-blue-200 py-2 px-4 font-bold text-blue-500 rounded hover:bg-blue-100">
            &gt;
        </button>
    </div>

    <!-- Dagen van de week -->
    <div class="grid grid-cols-7 gap-1">
        @foreach(['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag'] as $index => $day)
            @php
                $date = $this->currentDate->copy()->startOfWeek()->addDays($index);
            @endphp
            <div class="bg-blue-200 p-2 border border-gray-400">
                <div class="font-bold">{{ $day }}</div>
                <div class="text-sm">{{ $date->format('d-m-Y') }}</div>
            </div>
        @endforeach
    </div>

    <!-- Departementen en shifts -->
    @foreach($departments as $department)
        <div class="flex flex-col pt-4">
            <!-- Departement Naam -->
            <div class="bg-gray-400 p-2 text-white font-bold rounded-t">
                <h2>{{ $department->name }}</h2>
            </div>

            <!-- Commandant Label -->
            <div class="bg-gray-200 p-2 text-blue-400 text-sm font-bold">
                <h2>Commandant</h2>
            </div>

            <!-- Shifts per dag -->
            <div class="grid grid-cols-7 gap-1">
                @foreach(['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag'] as $index => $day)
                    @php
                        $date = $this->currentDate->copy()->startOfWeek()->addDays($index);
                        $filteredShifts = $shifts->filter(function($shift) use ($date, $department) {
                            return $shift->department_id === $department->id &&
                                   \Carbon\Carbon::parse($shift->start)->isSameDay($date);
                        });
                    @endphp
                    <div class="bg-white p-2 border border-gray-400 space-y-2 text-sm">
                        @forelse($filteredShifts as $shift)
                            <div class="border text-sm p-1">
                                <span class="font-bold">{{ $shift->name }}</span>
                                <ul class="list-disc list-inside">
                                    @foreach($shift->shiftschedules as $shiftschedule)
                                        <li>{{ $shiftschedule->user ? $shiftschedule->user->name : '' }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="text-gray-500">Geen shifts</div>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
