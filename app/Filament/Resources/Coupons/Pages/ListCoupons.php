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

    public function getTabs(): array
    {
        $user = auth()->user();

        return [
            'all' => Tab::make(__('coupon.tabs.all'))
                ->icon('heroicon-o-rectangle-stack')
                ->visible(!$user->roles->contains('slug', 'employee'))
                ->badge(CouponResource::getEloquentQuery()
                    ->count()),

            'Not scheduled' => Tab::make(__('coupon.tabs.not_scheduled'))
                ->icon('heroicon-o-rectangle-stack')
                ->visible($user->roles->contains('slug', 'employee'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('status'))
                ->badge(CouponResource::getEloquentQuery()
                    ->whereNull('status')
                    ->count()),

            'reserved ' => Tab::make(__('coupon.tabs.reserved'))
                ->icon('heroicon-o-clock')
                ->visible($user->roles->contains('slug', 'employee'))
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', Status::getReservedCases()))
                ->badge(CouponResource::getEloquentQuery()
                    ->whereIn('status', Status::getReservedCases())
                    ->count()),

            'scheduled' => Tab::make(__('coupon.tabs.scheduled'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', Status::getScheduledCases()))
                ->badge(CouponResource::getEloquentQuery()
                    ->whereIn('status', Status::getScheduledCases())
                    ->count()),

            'not_booked' => Tab::make(__('coupon.tabs.not_booked'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', Status::getNotBookedCases()))
                ->badge(CouponResource::getEloquentQuery()
                    ->whereIn('status', Status::getNotBookedCases())
                    ->count()),

            'booked' => Tab::make(__('coupon.tabs.booked'))
                ->icon('heroicon-o-check-circle')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status', Status::getBookedCases()))
                ->badge(CouponResource::getEloquentQuery()
                    ->whereIn('status', Status::getBookedCases())
                    ->count()),
        ];
    }

//    protected function getHeaderWidgets(): array
//    {
//        return [
//            \App\Filament\Widgets\MyCalendarWidget::class,
//        ];
//    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'scheduled';
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->color('primary'),
        ];
    }
}
