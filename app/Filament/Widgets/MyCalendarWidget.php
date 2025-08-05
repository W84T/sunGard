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
        $user = auth()->user();

        $coupons = Coupon::query()
            ->whereNotNull('reserved_date')
            ->when(
                ! $user->roles()->where('slug', 'admin')->exists(),
                function ($query) use ($user) {
                    $query->where(function ($q) use ($user) {
                        $q->where('agent_id', $user->id)
                            ->orWhere('employee_id', $user->id);

                        // Extend access for branch managers
                        if ($user->roles()->where('slug', 'branch manager')->exists() && $user->sungard_branch_id) {
                            $q->orWhere('sungard_branch_id', $user->sungard_branch_id);
                        }
                    });
                }
            )
            ->get();

        return $coupons->map(fn (Coupon $coupon) => CalendarEvent::make()
            ->title($coupon->customer_name)
            ->start($coupon->reserved_date)
            ->end($coupon->reserved_date)
        );
    }

}
