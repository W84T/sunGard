<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BranchCoupons;
use App\Filament\Widgets\GrowthWidget;
use App\Filament\Widgets\StateOverview;
use App\Filament\Widgets\TopAgentWidget;
use App\Models\Branch;
use App\Models\Exhibition;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class CustomDashboard extends Dashboard
{
    use HasFiltersForm;

    protected static ?int $navigationSort = -2;
    protected static ?string $title = 'Dashboard';

    public static function getNavigationIcon(): string|BackedEnum|null
    {
        return Phosphor::House->getIconForWeight(PhosphorWeight::Regular);
    }

    public static function getActiveNavigationIcon(): string|BackedEnum|null
    {
        return Phosphor::House->getIconForWeight(PhosphorWeight::Duotone);
    }



    public function filtersForm(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    DatePicker::make('startDate')
                        ->label('Start date')
                        ->default(now()->startOfYear())
                        ->native(false)
                        ->live(),

                    DatePicker::make('endDate')
                        ->label('End date')
                        ->default(now()->endOfYear())
                        ->native(false)
                        ->live(),

                    Select::make('exhibition_id')
                        ->label('Exhibition')
                        ->options(fn () => ['*' => 'All exhibitions'] + Exhibition::query()
                                ->orderBy('name')->pluck('name', 'id')->all())
                        ->default('*')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->wrapOptionLabels(false)
                        ->afterStateUpdated(fn (Set $set) => $set('branch_id', '*')),

                    Select::make('branch_id')
                        ->label('Branch')
                        ->options(function (Get $get) {
                            $exh = $get('exhibition_id') ?? '*';

                            if ($exh === '*' || $exh === null) {
                                return ['*' => 'All branches'] + Branch::query()
                                        ->orderBy('name')->pluck('name', 'id')->all();
                            }

                            $branches = Branch::query()
                                ->where('exhibition_id', $exh)
                                ->orderBy('name')
                                ->pluck('name', 'id')

                                ->all();

                            // Even if empty, keep '*' so the selection is valid, but it will show 0 in widgets.
                            return ['*' => 'All branches'] + $branches;
                        })
                        ->default('*')
                        ->searchable()
                        ->preload()
                        ->wrapOptionLabels(false)
                        ->live(),

                ])
                ->columns(4)
                ->columnSpanFull(),
        ]);
    }
    public function getWidgets(): array
    {
        return [
            StateOverview::class,
            GrowthWidget::class,
            BranchCoupons::class,
            TopAgentWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return 4;
    }

    public function persistsFiltersInSession(): bool
    {
        return false;
    }
}
