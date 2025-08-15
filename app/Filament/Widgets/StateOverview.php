<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Coupon;
use App\Models\User;
use App\Status;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class StateOverview extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
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

        // Agents total
        $agentsTotal = User::query()
            ->whereHas('roles', fn ($q) => $q->where('slug', 'agent'))
            ->when($branchId !== '*', fn ($q) => $q->where('branch_id', $branchId))
            ->when($branchId === '*' && $branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->count();

        // Not booked total
        $notBookedTotal = Coupon::query()
            ->whereIn('status', Status::getNotBookedCases())
            ->when($branchId !== '*', fn ($q) => $q->where('branch_id', $branchId))
            ->when($branchId === '*' && $branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Served total
        $servedTotal = Coupon::query()
            ->where('status', Status::CUSTOMER_SERVED->value)
            ->when($branchId !== '*', fn ($q) => $q->where('branch_id', $branchId))
            ->when($branchId === '*' && $branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds))
            ->whereBetween('created_at', [$start, $end])
            ->count();

        // Series
        $agentsSeries = $this->monthlyCounts(
            User::query()
                ->whereHas('roles', fn ($q) => $q->where('slug', 'agent'))
                ->when($branchId !== '*', fn ($q) => $q->where('branch_id', $branchId))
                ->when($branchId === '*' && $branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds)),
            'created_at',
            $start,
            $end
        );

        $notBookedSeries = $this->monthlyCounts(
            Coupon::query()
                ->whereIn('status', Status::getNotBookedCases())
                ->when($branchId !== '*', fn ($q) => $q->where('branch_id', $branchId))
                ->when($branchId === '*' && $branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds)),
            'created_at',
            $start,
            $end
        );

        $servedSeries = $this->monthlyCounts(
            Coupon::query()
                ->where('status', Status::CUSTOMER_SERVED->value)
                ->when($branchId !== '*', fn ($q) => $q->where('branch_id', $branchId))
                ->when($branchId === '*' && $branchIds !== null, fn ($q) => $q->whereIn('branch_id', $branchIds)),
            'created_at',
            $start,
            $end
        );

        return [
            Stat::make(__('widget.state_overview.agents.title'), number_format($agentsTotal))
                ->description(__('widget.state_overview.agents.description'))
                ->icon('heroicon-m-user-group')
                ->color('info')
                ->chart($agentsSeries),

            Stat::make(__('widget.state_overview.not_booked.title'), number_format($notBookedTotal))
                ->description(__('widget.state_overview.not_booked.description'))
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->chart($notBookedSeries),

            Stat::make(__('widget.state_overview.served.title'), number_format($servedTotal))
                ->description(__('widget.state_overview.served.description'))
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->chart($servedSeries),
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
