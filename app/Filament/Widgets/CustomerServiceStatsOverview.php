<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Coupon;
use App\Models\User;
use App\Status;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CustomerServiceStatsOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;
//    use HasWidgetShield;

    protected int | string | array $columnSpan = [
        'sm' => 4,
        'md' => 4,
    ];
    protected function getStats(): array
    {
        // Filters
        [$start, $end] = $this->getRange();
        $branchId = $this->filters['branch_id'] ?? '*';
        $exhibitionId = $this->filters['exhibition_id'] ?? '*';

        // Apply exhibition -> branches filter
        $branchIds = null;
        if ($exhibitionId !== '*') {
            $branchIds = Branch::query()
                ->where('exhibition_id', $exhibitionId)
                ->pluck('id')
                ->all();
        }


        $couponsTotal = Coupon::query()
            ->where('employee_id', Auth()->id())
            ->when($branchId !== '*', fn ($q) => $q->where('branch_id', $branchId))
            ->when($branchId === '*' && $branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->count();

        $couponSeries = $this->monthlyCounts(
            Coupon::query()
                ->where('employee_id', Auth()->id())
                ->when($branchId !== '*', fn ($q) => $q->where('branch_id', $branchId))
                ->when($branchId === '*' && $branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds)),
            'created_at',
            $start,
            $end
        );



        return [
            Stat::make(__('widget.state_overview.agents.title'), number_format($couponsTotal))
                ->description(__('widget.state_overview.agents.description'))
                ->icon('heroicon-m-user-group')
                ->color('info')
                ->chart($couponSeries),
        ];
    }

    protected function monthlyCounts(Builder $base, string $dateColumn, Carbon $start, Carbon $end): array
    {
        $driver = DB::getDriverName();
        $formatted = match ($driver) {
            'pgsql' => "to_char(date_trunc('month', {$dateColumn}), 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', {$dateColumn})",
            default => "DATE_FORMAT({$dateColumn}, '%Y-%m')",
        };

        $rows = (clone $base)
            ->whereBetween($dateColumn, [$start, $end])
            ->selectRaw("{$formatted} as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym')
            ->all();

        $series = [];
        $cursor = $start->copy()->startOfMonth();
        while ($cursor <= $end) {
            $ym = $cursor->format('Y-m');
            $series[] = (int) ($rows[$ym] ?? 0);
            $cursor->addMonth();
        }

        return $series;
    }

    private function getRange(): array
    {
        $start = isset($this->filters['startDate'])
            ? Carbon::parse($this->filters['startDate'])->startOfDay()
            : now()->startOfYear();

        $end = isset($this->filters['endDate'])
            ? Carbon::parse($this->filters['endDate'])->endOfDay()
            : now()->endOfYear();

        if ($end->lt($start)) {
            [$start, $end] = [$end, $start];
        }

        return [$start, $end];
    }
}
