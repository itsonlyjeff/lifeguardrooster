<div
    class="p-6 lg:p-8 rounded-2xl bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700 flex flex-col space-y-4 text-gray-900 dark:text-white">


    <div class="flex flex-col">
        @forelse($shifts as $shift)
            <div class="flex flex-col md:flex-row justify-between hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
                <div class="">
                    <div class="font-bold">
                        {{ $shift->name }}
                    </div>
                    <div class="">
                        {{ ucfirst(trans('days.' . $shift->start->format('l'))) }}
                        {{ $shift->start->format('d-m-Y') }}
                    </div>
                    <div>{{ $shift->start->format('H:i') }} - {{ $shift->end->format('H:i') }}</div>
                    <div class="py-3">
                        <span
                            class="mt-10 bg-purple-100 text-purple-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded dark:bg-purple-900 dark:text-purple-300">
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
                    </span><br />
                        <span
                            class="mt-10 text-xs font-medium me-2 px-2.5 py-0.5 rounded bg-orange-100" style="background-color: {{ $shift->shiftType->bg_color }}">
                            {{ ucfirst($shift->shiftType->name) }} - {{ $shift->department->name }}
                        </span>
                    </div>
                </div>

                <table>
                    @foreach($shift->shiftschedules as $schedule)
                        <tr class="align-top">
                            <td class="w-60 font-semibold">
                                <span>{{ ucfirst($schedule->role->name) }}</span><br />
                                <span class="md:hidden font-normal">{{ $schedule->user->name ?? '-' }}</span><br />
                                @if(isset($schedule->remarks))
                                    <span class="md:hidden text-xs text-gray-400">{{ $schedule->remarks }} </span>
                                @endif
                            </td>
                            <td class="hidden md:block w-60 md:flex md:flex-col">
                                <span>{{ $schedule->user->name ?? '-' }}</span>
                                @if(isset($schedule->remarks))
                                    <span class="text-xs text-gray-400">{{ $schedule->remarks }} </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>

            </div>
            <hr class="my-2"/>
        @empty
            <div class="p-10 flex flex-col items-center justify-center w-full">
                <img src="{{ asset('images/empty.png') }}" />
                <span class="text-gray-500">Er zijn geen diensten beschikbaar.</span>
            </div>
        @endforelse
    </div>
</div>
