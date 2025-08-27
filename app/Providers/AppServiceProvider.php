<?php

namespace App\Providers;

use App\Models\Coupon;
use App\Models\Ticket;
use App\Observers\CouponImageObserver;
use App\Observers\CouponObserver;
use App\Observers\TicketObserver;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
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
        Coupon::observe(CouponImageObserver::class);
        Ticket::observe(TicketObserver::class);
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['ar', 'en']);
        });

        //        FilamentAsset::register([
        //            Js::make('photoswipe', Vite::asset('resources/js/app.js'))->module(),
        //            Css::make('photoswipe', Vite::asset('resources/css/app.css')),
        //        ]);
    }
}
