<div>
    <div class="">
        <select
            class="gap-x-1.5 rounded-md bg-white px-10 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
            wire:model.change="selectedMonth">
            @foreach($months as $month)
                <option class="" value="{{ $month }}">{{ $month }}</option>
            @endforeach
        </select>

        <select
            class="gap-x-1.5 rounded-md bg-white px-10 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
            wire:model.change="selectedYear">
            @foreach($years as $year)
                <option class="" value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <h2 class="pt-10 text-xl font-bold">Financieel overzicht</h2>
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-6 py-3 rounded-s-lg">
                        Naam
                    </th>
                    <th scope="col" class="px-6 py-3">
                        IBAN
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Datum
                    </th>

                    <th scope="col" class="px-6 py-3 rounded-e-lg">
                        Bedrag
                    </th>
                </tr>
                </thead>
                <tbody>
                @php
                    $total_amount = 0;
                @endphp
                @foreach($usersWithPositiveAmount as $user)
{{--                    @dd($user)--}}
                    <tr class="bg-white dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $user->name }}
                        </th>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ isset($user->iban) ? $user->iban : '' }} {{ isset($user->iban_tnv) ? '('.$user->iban_tnv.')' : '' }}
                        </th>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <ul>
                                @foreach($user->shiftSchedules as $schedule)
                                    <li>-> {{ \Carbon\Carbon::parse($schedule->start)->format('d-m-Y') }} - {{ ucfirst($schedule->role->name) }} - &euro; {{ number_format($schedule->amount, 2, ',', '.') }}</li>
                                @endforeach
                            </ul>
                        </th>
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <span class="font-bold">&euro; {{ number_format($user->shiftschedules->sum('amount'), 2, ',', '.') }}</span>
                        </th>
                    </tr>
                    @php
                        $total_amount = $total_amount + $user->shiftschedules->sum('amount');
                    @endphp
                @endforeach
                </tbody>
                <tfoot>
                <tr class="font-semibold text-gray-900 dark:text-white">
                    <th scope="row" class="px-6 py-3 text-base">Totaal</th>
                    <td class="px-6 py-3"></td>
                    <td class="px-6 py-3"></td>
                    <td class="px-6 py-3">&euro; {{ number_format($total_amount, 2, ',', '.') }}</td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
