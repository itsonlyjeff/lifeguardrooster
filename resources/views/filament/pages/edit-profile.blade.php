<x-filament-panels::page>
    <div class="">
        <div class="space-y-6">
            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="max-w-xl">
                    @livewire('profile.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="max-w-xl">
                    @livewire('profile.update-iban')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="max-w-xl">
                    @livewire('profile.update-password')
                </div>
            </div>

{{--            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg">--}}
{{--                <div class="max-w-xl">--}}
{{--                    @livewire('profile.ical-link')--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow rounded-lg">--}}
{{--                <div class="max-w-xl">--}}
{{--                    @livewire('profile.two-factor-settings')--}}
{{--                </div>--}}
{{--            </div>--}}

        </div>
    </div>
</x-filament-panels::page>
