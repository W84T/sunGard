<?php

// also accepts a closure

namespace App\Providers\Filament;

use App\Filament\Pages\CouponsCalendarPage;
use App\Filament\Pages\CustomDashboard;
use App\Filament\Resources\Coupons\CouponResource;
use App\Filament\Widgets\MyCalendarWidget;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $user = auth()->user();

        return $panel
            ->default()
            ->colors([
                'primary' => [
                    50 => '255, 241, 233',    // Very light peach
                    100 => '255, 226, 209',   // Light pastel orange
                    200 => '255, 210, 182',   // Soft orange tint
                    300 => '255, 185, 143',   // Brighter tint
                    400 => '255, 151, 97',    // Light orange
                    500 => '243, 98, 30',     // Base: #f3621e
                    600 => '217, 80, 16',     // Slightly darker
                    700 => '191, 66, 10',     // Deeper orange
                    800 => '166, 55, 8',      // Burnt orange
                    900 => '140, 46, 6',      // Very dark orange
                    950 => '102, 30, 4',      // Almost brown
                ],
                // Pick your neutral. Stone = warmer, Zinc = cooler, Neutral = in-between
                'gray' => Color::Zinc,
            ])
            ->homeUrl('/dashboard')
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->brandName('Sun Gard')
            ->brandLogo(asset('storage/logo1.png'))
            ->favicon(asset('storage/favicon1.png'))
            ->brandLogoHeight('50px')
            ->id('admin')
            ->font(
                'Inter',
                url: asset('css/fonts.css'),
                provider: LocalFontProvider::class,
            )
            ->maxContentWidth('full')
            ->login()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                CustomDashboard::class,
//                CouponsCalendarPage::class,
            ])

            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([

                //                AccountWidget::class,
                //                FilamentInfoWidget::class,
                //                MyCalendarWidget::class,
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
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER, // or USER_MENU_BEFORE
                function (): string {
                    if (! auth()->user()?->can('create_coupons::coupon')) {
                        return '';
                    }

                    return Blade::render(<<<'BLADE'
                    <x-filament::button
                        tag="a"
                        size="md"
                        color="primary"
                        href="{{ \App\Filament\Resources\Coupons\CouponResource::getUrl('create') }}"
                    >
                        {{-- swap icon to your phosphor alias if desired --}}
                        <x-filament::icon icon="heroicon-m-plus-circle" class="me-1" />
                        {{ __('coupon.create') }}
                    </x-filament::button>
                BLADE);
                },
            )
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentApexChartsPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css');
    }


}
