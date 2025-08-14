<?php

namespace App\Filament\Pages;


use App\Filament\Widgets\MyCalendarWidget;
use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
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

    public function getWidgets(): array
    {
        return [
//            MyCalendarWidget::class,
        ];
    }

}
