<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class TopAgentWidget extends TableWidget
{
    protected static ?string $heading = 'Top Agents by Coupons Created';

    // Keep it 1 column wide
    protected int|string|array $columnSpan = 4;
    protected ?string $maxHeight = '300px';
    protected static bool $isLazy = true;

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => User::query()

                ->whereHas('roles', fn ($q) => $q->where('slug', 'agent'))
                ->withCount('couponsCreated')
                ->withMax('couponsCreated', 'created_at')
                ->orderByDesc('coupons_created_count')
            )
            ->columns([
               TextColumn::make('name')
                    ->label('Agent')
                    ->sortable(),

               TextColumn::make('coupons_created_count')
                    ->label('Coupons')
                    ->sortable()
                    ->badge(),

               TextColumn::make('coupons_created_max_created_at')
                    ->label('Last Coupon')
                    ->dateTime('Y-m-d H:i')
                    ->since()
                    ->sortable(),
            ])
            ->paginated();
    }
}

