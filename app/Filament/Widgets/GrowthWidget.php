<?php

namespace App\Filament\Widgets;

use App\Models\Coupon;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class GrowthWidget extends ChartWidget
{
    use InteractsWithPageFilters;
    use HasWidgetShield;
    protected ?string $heading = 'Coupons Growth';

    protected ?string $maxHeight = '300px';

    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = 2;

    public function getHeading(): ?string
    {
        return __('widget.growth.heading');
    }

    protected function getData(): array
    {
        [$start, $end] = $this->getRange();
        $branchId = $this->filters['branch_id'] ?? '*';

        // choose SQL snippet per driver
        $driver = DB::getDriverName();
        $ymExpr = match ($driver) {
            'pgsql' => "to_char(date_trunc('month', created_at), 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', created_at)",
            default => "DATE_FORMAT(created_at, '%Y-%m')", // mysql/mariadb
        };

        $query = Coupon::query()
            ->when($branchId !== '*', fn ($q) => $q->where('branch_id', $branchId))
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->selectRaw("$ymExpr as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->orderBy('ym');

        $counts = $query->pluck('total', 'ym')->all();

        // Build continuous month axis from start..end
        $labels = [];
        $data = [];

        $cursor = $start->copy()->startOfMonth();
        $last = $end->copy()->startOfMonth();

        while ($cursor <= $last) {
            $ym = $cursor->format('Y-m');
            $labels[] = $cursor->shortMonthName;        // Jan, Feb, ...
            $data[] = (int) ($counts[$ym] ?? 0);
            $cursor->addMonth();
        }

        return [
            'datasets' => [
                [
                    'label' => __('widget.growth.coupons'),
                    'data' => $data,
                    'tension' => 0.3,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    /** Resolve date range from page filters with sensible defaults. */
    private function getRange(): array
    {
        $start = isset($this->filters['startDate'])
            ? Carbon::parse($this->filters['startDate'])
            : now()->startOfYear();

        $end = isset($this->filters['endDate'])
            ? Carbon::parse($this->filters['endDate'])
            : now()->endOfYear();

        // normalize / guard
        $start = $start->startOfDay();
        $end = $end->endOfDay();

        if ($end->lt($start)) {
            [$start, $end] = [$end->copy()->startOfDay(), $start->copy()->endOfDay()];
        }

        return [$start, $end];
    }
}
