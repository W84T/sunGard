<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Coupon;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Support\Facades\DB;

class BranchCoupons extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Branch Coupons';

    protected int|string|array $columnSpan = 2;

    public function getHeading(): ?string
    {
        return __('widget.branch_coupons.heading');
    }

    protected ?string $maxHeight = '300px';

    // How many bars before grouping into "Other"
    protected int $maxSlices = 10;

    // HSL palette settings — same “saturation / brightness vibe” always
    protected int $hueSteps = 24;

    protected int $saturation = 70;

    protected int $lightness = 50;

    protected string $otherHex = '#9ca3af';

    protected function getData(): array
    {
        $start = isset($this->filters['startDate']) ? Carbon::parse($this->filters['startDate'])->startOfDay() : now()->startOfYear();
        $end = isset($this->filters['endDate']) ? Carbon::parse($this->filters['endDate'])->endOfDay() : now()->endOfYear();
        $branchId = $this->filters['branch_id'] ?? '*';
        $exhibitionId = $this->filters['exhibition_id'] ?? '*';

        // If exhibition is specific, compute the allowed branch IDs.
        $allowedBranchIds = null;
        if ($exhibitionId !== '*' && $exhibitionId !== null) {
            $allowedBranchIds = Branch::query()
                ->where('exhibition_id', $exhibitionId)
                ->pluck('id')
                ->all();

            // If the exhibition has NO branches -> return empty dataset.
            if (empty($allowedBranchIds)) {
                return ['labels' => [], 'datasets' => [['label' => __('widget.branch_coupons.coupons_per_branch'), 'data' => []]]];
            }
        }

        $rows = Coupon::query()
            // apply exhibition scope by branches list when exhibition is specific
            ->when(is_array($allowedBranchIds), fn ($q) => $q->whereIn('branch_id', $allowedBranchIds))
            // apply specific branch if chosen (and also ensure it belongs to the exhibition scope)
            ->when($branchId !== '*', function ($q) use ($branchId, $allowedBranchIds) {
                if (is_array($allowedBranchIds) && ! in_array((int) $branchId, $allowedBranchIds, true)) {
                    // chosen branch doesn't belong to chosen exhibition -> empty
                    $q->whereRaw('1 = 0');
                } else {
                    $q->where('branch_id', $branchId);
                }
            })
            ->whereBetween('created_at', [$start, $end])
            ->select('branch_id', DB::raw('COUNT(*) as total'))
            ->groupBy('branch_id')
            ->orderByDesc('total')
            ->get();

        // If nothing matched, return empty
        if ($rows->isEmpty()) {
            return ['labels' => [], 'datasets' => [['label' => __('widget.branch_coupons.coupons_per_branch'), 'data' => []]]];
        }

        // id => name
        $ids = $rows->pluck('branch_id')->filter()->all();
        $namesById = Branch::query()
            ->whereIn('id', $ids)
            ->pluck('name', 'id');

        $labels = [];
        $data = [];

        foreach ($rows as $row) {
            $labels[] = $row->branch_id
                ? ($namesById[$row->branch_id] ?? "Branch #{$row->branch_id}")
                : __('widget.branch_coupons.unassigned');
            $data[] = (int) $row->total;
        }

        // (optional) tail collapse ...
        if (count($labels) > $this->maxSlices) {
            $topLabels = array_slice($labels, 0, $this->maxSlices - 1);
            $topData = array_slice($data, 0, $this->maxSlices - 1);
            $otherSum = array_sum(array_slice($data, $this->maxSlices - 1));
            $labels = array_merge($topLabels, [__('widget.branch_coupons.other')]);
            $data = array_merge($topData, [$otherSum]);
        }

        // colors
        $bg = $border = [];
        foreach ($labels as $label) {
            $hex = ($label === 'Other') ? $this->otherHex : $this->colorForLabel($label);
            $bg[] = $hex;
            $border[] = $hex;
        }

        return [
            'labels' => $labels,
            'datasets' => [[
                'label' => 'Coupons per Branch',
                'data' => $data,
                'backgroundColor' => $bg,
                'borderColor' => $border,
                'borderWidth' => 1,
            ]],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function colorForLabel(string $label): string
    {
        $hash = crc32($label);
        $index = $hash % max(1, $this->hueSteps);
        $hue = (int) round(($index * (360 / $this->hueSteps)) % 360);

        return $this->hslToHex($hue, $this->saturation, $this->lightness);
    }

    protected function hslToHex(int $h, int $s, int $l): string
    {
        $s /= 100;
        $l /= 100;

        $c = (1 - abs(2 * $l - 1)) * $s;
        $x = $c * (1 - abs(fmod($h / 60, 2) - 1));
        $m = $l - $c / 2;

        [$r1, $g1, $b1] = match (true) {
            $h < 60 => [$c, $x, 0],
            $h < 120 => [$x, $c, 0],
            $h < 180 => [0, $c, $x],
            $h < 240 => [0, $x, $c],
            $h < 300 => [$x, 0, $c],
            default => [$c, 0, $x],
        };

        $r = (int) round(($r1 + $m) * 255);
        $g = (int) round(($g1 + $m) * 255);
        $b = (int) round(($b1 + $m) * 255);

        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'x' => ['beginAtZero' => true],
            ],
        ];
    }
}
