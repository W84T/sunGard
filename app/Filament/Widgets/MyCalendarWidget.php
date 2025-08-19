<?php

namespace App\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\FetchInfo;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class MyCalendarWidget extends CalendarWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;

    protected bool $eventClickEnabled = true;

    public function getHeading(): string
    {
        return __('calendar_page.heading');
    }

    protected ?string $defaultEventClickAction = 'view';

    public function getEvents(FetchInfo $info): Collection|Builder|array
    {
        $user = auth()->user();

        // Prefer FetchInfo dates, fallback to your page filters if set
        $start = $this->filters['startDate'] ?? $info->start;
        $end   = $this->filters['endDate']   ?? $info->end;
        $branch = $this->filters['branch_id'] ?? $user->sungard_branch_id;

        $start = Carbon::parse($start)->startOfDay();
        $end   = Carbon::parse($end)->endOfDay();

        $query = \App\Models\Coupon::query()
            ->select([
                'id',
                'reserved_date',
                'customer_name',
                'customer_phone',
                'car_brand',
                'car_model',
                'sungard_branch_id',
                'agent_id',
            ])
            ->with(['sungard:id,name,color'])
            ->whereNotNull('reserved_date')
            ->whereBetween('reserved_date', [$start, $end]);

        if ($user->hasRoleSlug('agent')) {
            $query->where('agent_id', $user->id);
        } elseif ($user->hasRoleSlug('branch manager')) {
            $query->where('sungard_branch_id', $user->sungard_branch_id);
        } elseif ($branch && $branch !== '*') {
            $query->where('sungard_branch_id', $branch);
        }

        $coupons = $query->orderBy('reserved_date')->get();

        return $coupons->map(function ($coupon) {
            $bg = $coupon->sungard?->color ?: '#9CA3AF';

            return CalendarEvent::make($coupon)
                ->title($coupon->customer_name)
                ->start($coupon->reserved_date)
                ->end($coupon->reserved_date)
                ->backgroundColor($bg)
                ->extendedProps([
                    'tooltip' => implode("\n", array_filter([
                        __('calendar_page.tooltip.customer', ['customer_name' => $coupon->customer_name]),
                        __('calendar_page.tooltip.phone', ['customer_phone' => $coupon->customer_phone]),
                        __('calendar_page.tooltip.car', ['car_brand' => $coupon->car_brand, 'car_model' => $coupon->car_model]),
                        optional($coupon->sungard)->name ? __('calendar_page.tooltip.branch', ['branch_name' => $coupon->sungard->name]) : null,
                    ])),
                ])
                ->action('view');
        });
    }

    public function getEventContent(): null|string|array
    {
        return new HtmlString(<<<'HTML'
            <div class="flex items-center truncate"
                 x-bind:title="event.extendedProps?.tooltip || event.title">
                <span class="truncate" x-text="event.title"></span>
            </div>
        HTML
        );
    }
}
