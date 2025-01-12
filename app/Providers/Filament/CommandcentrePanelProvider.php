<?php

namespace App\Providers\Filament;

use App\Filament\Commandcentre\Pages\HealthCheckResults;
use App\Filament\Pages\EditProfile;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use ShuvroRoy\FilamentSpatieLaravelHealth\FilamentSpatieLaravelHealthPlugin;

class CommandcentrePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('commandcentre')
            ->path('commandcentre')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->login()
            ->discoverResources(in: app_path('Filament/Commandcentre/Resources'), for: 'App\\Filament\\Commandcentre\\Resources')
            ->discoverPages(in: app_path('Filament/Commandcentre/Pages'), for: 'App\\Filament\\Commandcentre\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Commandcentre/Widgets'), for: 'App\\Filament\\Commandcentre\\Widgets')
            ->widgets([
                //
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationItems([
                NavigationItem::make('Log Viewer')
                    ->url(config('app.url').'/log-viewer')
                    ->icon('heroicon-o-information-circle')
                    ->group('Monitoring')
                    ->sort(3)
                    ->openUrlInNewTab(true),
                NavigationItem::make('Pulse')
                    ->url(config('app.url').'/pulse')
                    ->icon('heroicon-o-tag')
                    ->group('Monitoring')
                    ->sort(3)
                    ->openUrlInNewTab(true),
                NavigationItem::make('Horizon')
                    ->url(config('app.url').'/horizon')
                    ->icon('heroicon-o-queue-list')
                    ->group('Monitoring')
                    ->sort(3)
                    ->openUrlInNewTab(true),
                NavigationItem::make('Telescope')
                    ->url(config('app.url').'/telescope')
                    ->icon('heroicon-o-beaker')
                    ->group('Monitoring')
                    ->sort(3)
                    ->openUrlInNewTab(true),
            ])
            ->plugins([
                FilamentSpatieLaravelHealthPlugin::make()
                    ->usingPage(HealthCheckResults::class),
            ])
            ->maxContentWidth('full')
            ->spa()
            ->viteTheme('resources/css/filament/commandcentre/theme.css');

    }
}
