<div
    class="p-6 lg:p-8 rounded-2xl bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700 flex flex-col space-y-4 text-gray-900 dark:text-white">

    @forelse($shifts as $shift)
        <div
            class="p-5 rounded-2xl flex flex-col md:flex-row justify-between bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 hover:dark:bg-gray-500">
            <div class="flex flex-col">
                <span class="font-bold">
                    {{ $shift->name }}
                </span>
                <span class="font-semibold">
                {{ ucfirst(trans('days.' . $shift->start->format('l'))) }}
                    {{ $shift->start->format('d-m-Y') }}
            </span>
                <span>
                {{ $shift->start->format('H:i') }} - {{ $shift->end->format('H:i') }}
            </span>
                <div class="flex flex-row space-x-1">
                <span
                    class="bg-purple-100 text-purple-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">
                   @if($shift->start_scheduling)
                        @if($shift->start_scheduling->isPast())
                            <!-- Message when the scheduling is in the past -->
                            Direct automatisch roosteren
                        @else
                            <!-- Message when the scheduling is in the future -->
                            Automatisch Roosteren {{ $shift->start_scheduling->diffForHumans() }}
                        @endif
                    @else
                        Manueel Roosteren
                    @endif
                </span>
                </div>

            </div>

            <div class="pt-5 md:pt-0">
                <button wire:click="setAvailability('{{ $shift->id }}', true)" type="button"
                        class="focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-800">
                    {{ __('shifts.button-available') }}</button>
                <button wire:click="setAvailability('{{ $shift->id }}', false)" type="button"
                        class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">{{ __('shifts.button-not-available') }}</button>
            </div>
        </div>
    @empty
        <div class="p-10 flex flex-col items-center justify-center w-full">
            <img src="{{ asset('images/empty.png') }}" />
            <span class="text-gray-500">{{ __('shifts.list-empty') }}</span>
        </div>
    @endforelse

</div>
