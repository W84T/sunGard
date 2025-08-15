<?php

namespace App\Filament\Widgets;

use App\Models\Coupon;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GrowthWidget extends ChartWidget
{
    protected ?string $heading = 'Coupons Growth (This Year)';
//    protected ?string $maxHeight = '300px';
    protected static bool $isLazy = true;

    protected int|string|array $columnSpan = 2;
    // Optional: refresh automatically
    // protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $year = now()->year;
        $driver = DB::getDriverName();

        $query = match ($driver) {
            'pgsql'  => Coupon::query()
                ->selectRaw("to_char(date_trunc('month', created_at), 'YYYY-MM') as ym, COUNT(*) as total"),
            'sqlite' => Coupon::query()
                ->selectRaw("strftime('%Y-%m', created_at) as ym, COUNT(*) as total"),
            default  => Coupon::query() // mysql/mariadb
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total"),
        };

        $counts = $query
            ->whereYear('created_at', $year)
            ->groupBy('ym')
            ->orderBy('ym')
            ->pluck('total', 'ym')
            ->all();

        $labels = [];
        $data   = [];

        for ($m = 1; $m <= 12; $m++) {
            $ym = sprintf('%04d-%02d', $year, $m);
            $labels[] = Carbon::create($year, $m, 1)->shortMonthName; // Jan, Feb, ...
            $data[]   = $counts[$ym] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => "Coupons ($year)",
                    'data'  => $data,
                     'tension' => .3,
                     'fill' => true,

                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line'; // 'bar' also works nicely for monthly counts
    }
}
