<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TopAgentWidget extends TableWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Top Agents by Coupons Created';

    // Full width
    protected int|string|array $columnSpan = 4;

    protected ?string $maxHeight = '300px';

    protected static bool $isLazy = true;

    public function getHeading(): ?string
    {
        return __('widget.top_agent.heading');
    }

    public function table(Table $table): Table
    {
        // Read filters with defaults
        $start = isset($this->filters['startDate'])
            ? Carbon::parse($this->filters['startDate'])->startOfDay()
            : now()->startOfYear();

        $end = isset($this->filters['endDate'])
            ? Carbon::parse($this->filters['endDate'])->endOfDay()
            : now()->endOfYear();

        $exhibitionId = $this->filters['exhibition_id'] ?? '*';
        $branchId = $this->filters['branch_id'] ?? '*';

        return $table
            ->query(function () use ($start, $end, $exhibitionId, $branchId): Builder {
                return User::query()
                    ->whereHas('roles', fn ($q) => $q->where('slug', 'agent'))

                    // Count coupons created in range + filters
                    ->withCount([
                        'couponsCreated as coupons_created_count' => function ($q) use ($start, $end, $exhibitionId, $branchId) {
                            $q->whereBetween('created_at', [$start, $end])
                                ->when($exhibitionId !== '*', fn ($qq) => $qq->where('exhibition_id', $exhibitionId))
                                ->when($branchId !== '*', fn ($qq) => $qq->where('branch_id', $branchId));
                        },
                    ])

                    // Max created_at (last coupon date) in range + filters
                    ->withMax([
                        'couponsCreated' => function ($q) use ($start, $end, $exhibitionId, $branchId) {
                            $q->whereBetween('created_at', [$start, $end])
                                ->when($exhibitionId !== '*', fn ($qq) => $qq->where('exhibition_id', $exhibitionId))
                                ->when($branchId !== '*', fn ($qq) => $qq->where('branch_id', $branchId));
                        },
                    ], 'created_at')

                    // Hide agents with zero coupons in period
                    ->having('coupons_created_count', '>', 0)

                    ->orderByDesc('coupons_created_count');
            })
            ->columns([
                TextColumn::make('name')
                    ->label(__('widget.top_agent.agent'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('coupons_created_count')
                    ->label(__('widget.top_agent.coupons'))
                    ->sortable()
                    ->badge(),

                TextColumn::make('coupons_created_max_created_at')
                    ->label(__('widget.top_agent.last_coupon'))
                    ->dateTime('Y-m-d H:i')
                    ->since()
                    ->sortable(),
            ])
            ->paginated([5, 10, 25])
            ->defaultPaginationPageOption(10);
    }
}
