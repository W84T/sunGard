<?php

namespace App\Providers;

use App\Models\Coupon;
use App\Observers\CouponObserver;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Coupon::observe(CouponObserver::class);
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en']);
        });

        FilamentAsset::register([
            Js::make('photoswipe', Vite::asset('resources/js/app.js'))->module(),
            Css::make('photoswipe', Vite::asset('resources/css/app.css')),
        ]);
    }
}
