<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MyCalendarWidget;
use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;

class CouponsCalendarPage extends Dashboard
{
    use HasFiltersForm;
    protected static ?int $navigationSort = -1;
    // 👇 This gives it its own route
    protected static string $routePath = 'calendar';

    protected static ?string $title = 'Calendar';

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Phosphor::Calendar->getIconForWeight(PhosphorWeight::Regular);
    }

    public static function getActiveNavigationIcon(): string|BackedEnum|null
    {
        return Phosphor::Calendar->getIconForWeight(PhosphorWeight::Duotone);
    }

    public function filtersForm(Schema $schema): Schema
    {
        $user = auth()->user();

        return $schema->components([
            Section::make()
                ->schema([
                    ToggleButtons::make('range')
                        ->label('')
                        ->inline()
                        ->options([
                            'today' => 'Today',
                            'this_week' => 'This week',
                            'this_month' => 'This month',
                            'next_3_months' => 'Next 3 months',
                            'custom' => 'Custom',
                        ])
                        ->default('today')
                        ->live()
                        ->afterStateUpdated(function (Set $set, string $state) {
                            $now = now();

                            if ($state === 'custom') {
                                $this->dispatch('open-modal', id: 'customDateModal');
                            }

                            match ($state) {
                                'today' => [
                                    $set('startDate', $now->copy()->startOfDay()->toDateString()),
                                    $set('endDate', $now->copy()->endOfDay()->toDateString()),
                                ],
                                'this_week' => [
                                    $set('startDate', $now->copy()->startOfWeek(Carbon::MONDAY)->toDateString()),
                                    $set('endDate', $now->copy()->endOfWeek(Carbon::MONDAY)->toDateString()),
                                ],
                                'this_month' => [
                                    $set('startDate', $now->copy()->startOfMonth()->toDateString()),
                                    $set('endDate', $now->copy()->endOfMonth()->toDateString()),
                                ],
                                'next_3_months' => [
                                    $set('startDate', $now->copy()->startOfDay()->toDateString()),
                                    $set('endDate', $now->copy()->addMonthsNoOverflow(3)->endOfDay()->toDateString()),
                                ],
                            };

                            $this->dispatch('calendar--refresh');
                        }),

                    Hidden::make('startDate')->default(fn() => now()->startOfDay()->toDateString()),
                    Hidden::make('endDate')->default(fn() => now()->endOfDay()->toDateString()),

                    Select::make('branch_id')
                        ->label('Branch')
                        ->options(fn () => ['*' => 'All branches'] + \App\Models\SungardBranches::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                        ->default(function () use ($user) {
                            if ($user->hasRoleSlug('branch manager')) {
                                return $user->sungard_branch_id;
                            }
                            return '*';
                        })
                        ->disabled(fn () => $user->hasRoleSlug('branch manager'))
                        ->hidden(fn () => $user->hasRoleSlug('agent'))
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn () => $this->dispatch('calendar--refresh')),
                ])
                ->columns(2)
                ->extraAttributes(['class' => 'flex justify-between gap-3 flex-wrap'])
                ->columnSpanFull(),
        ]);
    }

    public function getWidgets(): array
    {
        return [
            MyCalendarWidget::class,
        ];
    }
}
