<div>
    <div class="">
        <select
            class="gap-x-1.5 rounded-md bg-white px-10 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
            wire:model.change="selectedYear">
            @foreach($years as $year)
                <option class="" value="{{ $year }}">{{ $year }}</option>
            @endforeach
        </select>
    </div>

    <h2 class="pt-10 text-xl font-bold">Vergoedingen</h2>
    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3 rounded-s-lg">
                    Datum
                </th>
                <th scope="col" class="px-6 py-3">
                    Rol
                </th>
                <th scope="col" class="px-6 py-3 rounded-e-lg">
                    Vergoeding
                </th>
            </tr>
            </thead>
            <tbody>
            @forelse($shiftschedules as $shiftschedule)
                <tr class="bg-white dark:bg-gray-800">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ ucfirst(trans('days.' . $shiftschedule->shift->start->format('l'))) }} {{ $shiftschedule->shift->start->format('d-m-Y') }}
                    </th>
                    <td class="px-6 py-4">
                        {{ ucfirst($shiftschedule->role->name) }}
                    </td>
                    <td class="px-6 py-4">
                        &euro; {{ number_format($shiftschedule->amount, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr class="bg-white dark:bg-gray-800">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Geen vergoedingen gevonden.
                    </th>
                    <td class="px-6 py-4">
                        &nbsp;
                    </td>
                    <td class="px-6 py-4">
                        &nbsp;
                    </td>
                </tr>
            @endforelse

            </tbody>
            <tfoot>
            <tr class="font-semibold text-gray-900 dark:text-white">
                <th scope="row" class="px-6 py-3 text-base">Totaal</th>
                <td class="px-6 py-3"></td>
                <td class="px-6 py-3">&euro; {{ number_format($total_amount_shiftschedules, 2, ',', '.') }}</td>
            </tr>
            </tfoot>
        </table>
    </div>





    <h2 class="pt-10 text-xl font-bold">Goedgekeurde Declaraties</h2>
    <div class="relative overflow-x-auto">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-6 py-3 rounded-s-lg">
                    Datum
                </th>
                <th scope="col" class="px-6 py-3">
                    Omschrijving
                </th>
                <th scope="col" class="px-6 py-3 rounded-e-lg">
                    Bedrag
                </th>
            </tr>
            </thead>
            <tbody>
            @forelse($expenses as $expense)
                <tr class="bg-white dark:bg-gray-800">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        {{ ucfirst(trans('days.' . $expense->date_expense->format('l'))) }} {{ $expense->date_expense->format('d-m-Y') }}
                    </th>
                    <td class="px-6 py-4">
                        {{ ucfirst($expense->description) }}
                    </td>
                    <td class="px-6 py-4">
                        &euro; {{ number_format($expense->amount, 2, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr class="bg-white dark:bg-gray-800">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        Geen goedgekeurde declaraties gevonden.
                    </th>
                    <td class="px-6 py-4">
                        &nbsp;
                    </td>
                    <td class="px-6 py-4">
                        &nbsp;
                    </td>
                </tr>
            @endforelse

            </tbody>
            <tfoot>
            <tr class="font-semibold text-gray-900 dark:text-white">
                <th scope="row" class="px-6 py-3 text-base">Totaal</th>
                <td class="px-6 py-3"></td>
                <td class="px-6 py-3">&euro; {{ number_format($total_amount_expenses, 2, ',', '.') }}</td>
            </tr>
            </tfoot>
        </table>
    </div>

</div>
