<?php

namespace Modules\Admin\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use Filament\Navigation\NavigationGroup;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('web')
            ->brandLogo(asset('images/logo/logo.png'))
            ->brandLogoHeight('2.5rem')
            ->brandName('iGate Admin')
            ->favicon(asset('favicon.ico'))
            ->colors([
                'primary' => Color::hex('#000000'),
                'gray' => Color::Gray,
            ])
            ->darkMode(false)
            ->font('Poppins')
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Operations'),
                NavigationGroup::make()
                    ->label('Marketplace'),
                NavigationGroup::make()
                    ->label('Financials'),
                NavigationGroup::make()
                    ->label('Clients'),
                NavigationGroup::make()
                    ->label('Service Provider'),
                NavigationGroup::make()
                    ->label('Audit Logs'),
                NavigationGroup::make()
                    ->label('System Settings'),
                NavigationGroup::make()
                    ->label('Website Management'),
                NavigationGroup::make()
                    ->label('Support Management'),
                NavigationGroup::make()
                    ->label('Identity'),
            ])
            ->discoverResources(in: base_path('Modules/Admin/app/Filament/Resources'), for: 'Modules\\Admin\\Filament\\Resources')
            ->discoverPages(in: base_path('Modules/Admin/app/Filament/Pages'), for: 'Modules\\Admin\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: base_path('Modules/Admin/app/Filament/Widgets'), for: 'Modules\\Admin\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class, // Removed to prioritize stats
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
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
