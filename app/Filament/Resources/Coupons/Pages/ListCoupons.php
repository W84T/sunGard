<?php

namespace App\Filament\Resources\Coupons\Pages;

use App\Filament\Resources\Coupons\CouponResource;
use App\Status;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCoupons extends ListRecords
{
    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('coupon.tabs.all'))
                ->icon('heroicon-o-rectangle-stack')
                ->badge(CouponResource::getEloquentQuery()
                    ->count()),
            'scheduled' => Tab::make(__('coupon.tabs.scheduled'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', Status::getScheduledCases()))
                ->badge(CouponResource::getEloquentQuery()
                    ->whereIn('status', Status::getScheduledCases())
                    ->count()),
            'not_booked' => Tab::make(__('coupon.tabs.not_booked'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', Status::getNotBookedCases()))
                ->badge(CouponResource::getEloquentQuery()
                    ->whereIn('status', Status::getNotBookedCases())
                    ->count()),
            'booked' => Tab::make(__('coupon.tabs.booked'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', Status::getBookedCases()))
                ->badge(CouponResource::getEloquentQuery()
                    ->whereIn('status', Status::getBookedCases())
                    ->count()),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'scheduled';
    }
}
