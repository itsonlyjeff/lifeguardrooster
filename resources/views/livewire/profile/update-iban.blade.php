<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Financiele Informatie') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Wijzig je rekeningnummer. Deze wordt gebruikt voor de betalingen van de vergoeding Quackstrand.") }}
        </p>
    </header>

    <form wire:submit="updateIban" class="mt-6 space-y-6">
        <div>
            <x-input-label for="iban" :value="__('Iban nummer')" />
            <x-text-input wire:model="iban" id="iban" name="iban" type="text" class="mt-1 block w-full" required autocomplete="iban" />
            <x-input-error class="mt-2" :messages="$errors->get('iban')" />

        </div>

        <div class="flex items-center gap-4">
            <x-filament::button type="submit">{{ __('Save') }}</x-filament::button>

            <x-action-message class="me-3" on="iban-updated">
                {{ __('Saved.') }}
            </x-action-message>
        </div>

    </form>
</section>
