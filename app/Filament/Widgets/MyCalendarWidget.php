<?php

namespace App\Filament\Widgets;

use App\Models\Coupon;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\Widgets\CalendarWidget;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;


class MyCalendarWidget extends CalendarWidget
{
    protected string|\Closure|null|HtmlString $heading = 'Coupons Calendar';

    // 1) enable event-clicks + make default click open Edit modal
    protected bool $eventClickEnabled = true;
    protected ?string $defaultEventClickAction = 'edit';

    public function getEvents(array $fetchInfo = []): \Illuminate\Support\Collection|array
    {
        $user = auth()->user();

        $coupons = \App\Models\Coupon::query()
            ->with('sungard:id,name,color')
            ->whereNotNull('reserved_date')
            ->when(! $user->roles()->where('slug', 'admin')->exists(), function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('agent_id', $user->id)
                        ->orWhere('employee_id', $user->id);

                    if ($user->roles()->where('slug', 'branch manager')->exists() && $user->sungard_branch_id) {
                        $q->orWhere('sungard_branch_id', $user->sungard_branch_id);
                    }
                });
            })
            ->get();

        return $coupons->map(function ($coupon) {
            $bg = $coupon->sungard?->color ?: '#9CA3AF';

            return CalendarEvent::make($coupon)          // 2) pass the Eloquent model so key/model are set
            ->title($coupon->customer_name)
                ->start($coupon->reserved_date)
                ->end($coupon->reserved_date)
                ->backgroundColor($bg)                   // branch color
                ->extendedProps([
                    'tooltip' => implode("\n", array_filter([
                        "Customer: {$coupon->customer_name}",
                        "Phone: {$coupon->customer_phone}",
                        "Car: {$coupon->car_brand} {$coupon->car_model}",
                        optional($coupon->sungard)->name ? "Branch: {$coupon->sungard->name}" : null,
                    ])),
                ])
                ->action('edit');                        // optional (redundant if defaultEventClickAction set)
        });
    }

    // Native hover tooltip
    public function getEventContent(): null|string|array
    {
        return new HtmlString(<<<'HTML'
            <div class="flex items-center truncate"
                 x-bind:title="event.extendedProps?.tooltip || event.title">
                <span class="truncate" x-text="event.title"></span>
            </div>
        HTML);
    }
}
