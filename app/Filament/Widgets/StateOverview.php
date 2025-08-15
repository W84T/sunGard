<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Branch;
use App\Models\Coupon;
use App\Status;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StateOverview extends StatsOverviewWidget
{
//    protected int|string|array $columnSpan = 1;

    protected function getStats(): array
    {
        // Totals
        $agentsTotal   = User::query()
            ->whereHas('roles', fn ($q) => $q->where('slug', 'agent'))
            ->count();

        $branchesTotal = Branch::query()->count();

        $servedTotal   = Coupon::query()
            ->where('status', Status::CUSTOMER_SERVED->value)
            ->count();

        // Series
        $agentsSeries   = $this->monthlyCounts(User::query()->whereHas('roles', fn ($q) => $q->where('slug', 'agent')));
        $servedSeries   = $this->monthlyCounts(Coupon::query()->where('status', Status::CUSTOMER_SERVED->value));

        // Branches: cumulative (running total) over last 12 months
        $branchesNewPerMonth = $this->monthlyCounts(Branch::query()); // monthly new
        $branchesCumulative  = $this->toCumulative($branchesNewPerMonth);

        return [
            Stat::make('Agents (Total)', number_format($agentsTotal))
                ->description('Total agents • last 12 mo trend')
                ->icon('heroicon-m-user-group')
                ->color('info')
                ->chart($agentsSeries),

            Stat::make('Branches (Total)', number_format($branchesTotal))
                ->description('Cumulative branches • last 12 mo')
                ->icon('heroicon-m-building-storefront')
                ->color('warning')
                ->chart($branchesCumulative),

            Stat::make('Served (Total)', number_format($servedTotal))
                ->description('Total served • last 12 mo trend')
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->chart($servedSeries),
        ];
    }

    /**
     * Monthly counts for the last N months (oldest -> newest).
     */
    protected function monthlyCounts(Builder $base, string $dateColumn = 'created_at', int $months = 12): array
    {
        $end   = Carbon::now()->startOfMonth();
        $start = (clone $end)->subMonths($months - 1);

        $driver = DB::getDriverName();
        $formatted = match ($driver) {
            'pgsql'  => "to_char(date_trunc('month', {$dateColumn}), 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', {$dateColumn})",
            default  => "DATE_FORMAT({$dateColumn}, '%Y-%m')", // mysql/mariadb
        };

        $rows = (clone $base)
            ->whereBetween($dateColumn, [$start, (clone $end)->endOfMonth()])
            ->selectRaw("{$formatted} as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym')
            ->all();

        $series = [];
        for ($i = 0; $i < $months; $i++) {
            $ym = $start->copy()->addMonths($i)->format('Y-m');
            $series[] = (int)($rows[$ym] ?? 0);
        }

        return $series;
    }

    /**
     * Convert a monthly series into a cumulative (running total) series.
     */
    protected function toCumulative(array $series): array
    {
        $sum = 0;
        foreach ($series as $i => $v) {
            $sum += (int) $v;
            $series[$i] = $sum;
        }
        return $series;
    }
}
