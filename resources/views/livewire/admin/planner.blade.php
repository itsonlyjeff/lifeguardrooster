<div class="flex flex-col">
    <!-- Navigatie voor weken -->
    <div class="flex space-x-1 items-center mb-4">
        <button wire:click="previousWeek"
                class="bg-blue-200 dark:bg-blue-800 py-2 px-4 font-bold text-blue-500 rounded hover:bg-blue-100">
            &lt;
        </button>

        <span class="bg-blue-200 dark:bg-blue-800 py-2 px-4 font-bold rounded">
             Wk {{ $this->currentWeek }}, {{ \Carbon\Carbon::create($this->currentYear, $this->currentMonth)->format('M') }} {{ $this->currentYear }}
        </span>

        <button wire:click="backToToday"
                class="bg-blue-200 dark:bg-blue-800 py-2 px-4 font-bold text-blue-500 rounded hover:bg-blue-100">
            <x-css-today/>
        </button>

        <button wire:click="nextWeek" class="bg-blue-200 dark:bg-blue-800 py-2 px-4 font-bold text-blue-500 rounded hover:bg-blue-100">
            &gt;
        </button>
    </div>

    <!-- Dagen van de week -->
    <div class="grid grid-cols-7 gap-1">
        @foreach(['Maandag', 'Dinsdag', 'Woensdag', 'Donderdag', 'Vrijdag', 'Zaterdag', 'Zondag'] as $index => $day)
            @php
                $date = $this->currentDate->copy()->startOfWeek()->addDays($index);
            @endphp
            <div class="bg-blue-200 dark:bg-blue-800 p-2 border border-gray-400">
                <div class="font-bold">{{ $day }}</div>
                <div class="text-sm">{{ $date->format('d-m-Y') }}</div>
            </div>
        @endforeach
    </div>

    <!-- Departementen en shifts -->
    @foreach($departments as $department)
        <div class="flex flex-col pt-4">
            <!-- Departement Naam -->
            <div class="bg-gray-400 dark:bg-gray-600 p-2 text-white font-bold rounded-t">
                <h2>{{ $department->name }}</h2>
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
                    <div class="flex flex-col bg-white dark:bg-gray-400 p-2 border border-gray-400 space-y-2 text-sm">
                        @forelse($filteredShifts as $shift)
                            <a href="{{ route('filament.admin.resources.shifts.edit', [\Filament\Facades\Filament::getTenant()->slug, $shift->id]) }}">
                                <div class="border text-sm p-2 rounded-xl flex flex-col"
                                     style="background-color: {{ $shift->shiftType->bg_color }}">
                                    <span class="font-bold text-black">{{ $shift->name }}</span>
                                    <span class="text-black text-xs">{{ \Carbon\Carbon::parse($shift->start)->format('H:i') }} - {{ \Carbon\Carbon::parse($shift->end)->format('H:i') }} uur</span>
                                    <div class="pt-2" style="display: inline-flex;">
                                        <span
                                            style="padding-left: 5px; padding-right: 5px; background-color: #ffff00; color: #ff8000; font-size: 12px; font-weight: 1000; border-radius: 4px;">
                                            #{{ $shift->shiftType->name }}
                                        </span>
                                    </div>
                                    <div class="pt-3 flex flex-col space-y-3">
                                        @foreach($shift->shiftschedules as $shiftschedule)
                                            <div class="border text-xs p-1 flex flex-col rounded"
                                                 style="background-color: {{ $shiftschedule->user ? '#74B72E' : 'red' }};">
                                                <span
                                                    class="font-bold text-gray-700">{{ $shiftschedule->role->name }}</span>
                                                <span>{{ $shiftschedule->user ? $shiftschedule->user->name : 'Open dienst' }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-gray-500 dark:text-gray-200">Geen dienst gepland.</div>
                        @endforelse
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>
