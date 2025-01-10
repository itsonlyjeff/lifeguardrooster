import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/Commandcentre/**/*.php',
        './resources/views/filament/commandcentre/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        './resources/views/components/**/*.blade.php',
        './resources/views/livewire/**/*.blade.php',
        './resources/views/livewire/*.blade.php',
    ],
}
