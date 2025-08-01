<?php

namespace App\Filament\Widgets;

use App\Models\Coupon;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\Widgets\CalendarWidget;
use Illuminate\Support\Collection;

class MyCalendarWidget extends CalendarWidget
{
    public function getEvents(array $fetchInfo = []): Collection|array
    {
        return Coupon::whereNotNull('reserved_date')
            ->get()
            ->map(function (Coupon $coupon) {
                return CalendarEvent::make()
                    ->title($coupon->customer_name)
                    ->start($coupon->reserved_date)
                    ->end($coupon->reserved_date);
            });
    }
}
