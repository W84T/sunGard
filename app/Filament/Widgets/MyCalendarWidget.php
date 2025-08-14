<?php

namespace App\Filament\Widgets;

use Closure;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Guava\Calendar\Widgets\CalendarWidget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;

class MyCalendarWidget extends CalendarWidget
{
    use InteractsWithPageFilters;

    protected string|Closure|null|HtmlString $heading = 'Coupons Calendar';

    protected bool $eventClickEnabled = true;
    protected ?string $defaultEventClickAction = 'view';

    public function getEvents(array $fetchInfo = []): Collection|array
    {
        $user = auth()->user();
        $start = $this->filters['startDate'] ?? now()->startOfMonth();
        $end = $this->filters['endDate'] ?? now()->endOfMonth();
        $branch = $this->filters['branch_id'] ?? $user->sungard_branch_id;

        $start = Carbon::parse($start)->startOfDay();
        $end = Carbon::parse($end)->endOfDay();

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
            // Only own coupons
            $query->where('agent_id', $user->id);
        } elseif ($user->hasRoleSlug('branch manager')) {
            // Force branch
            $query->where('sungard_branch_id', $user->sungard_branch_id);
        } elseif ($branch && $branch !== '*') {
            // Manual filter
            $query->where('sungard_branch_id', $branch);
        }

        $coupons = $query->orderBy('reserved_date')->get();

        return $coupons->map(function ($coupon) {
            $bg = $coupon->sungard?->color ?: '#9CA3AF';

            return \Guava\Calendar\ValueObjects\CalendarEvent::make($coupon)
                ->title($coupon->customer_name)
                ->start($coupon->reserved_date)
                ->end($coupon->reserved_date)
                ->backgroundColor($bg)
                ->extendedProps([
                    'tooltip' => implode("\n", array_filter([
                        "Customer: {$coupon->customer_name}",
                        "Phone: {$coupon->customer_phone}",
                        "Car: {$coupon->car_brand} {$coupon->car_model}",
                        optional($coupon->sungard)->name ? "Branch: {$coupon->sungard->name}" : null,
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
