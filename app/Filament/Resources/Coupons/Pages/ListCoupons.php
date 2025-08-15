<?php

namespace App\Filament\Resources\Coupons\Pages;

use App\Filament\Exports\CouponExporter;
use App\Filament\Resources\Coupons\CouponResource;
use App\Models\Coupon;
use App\Status;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCoupons extends ListRecords
{
    protected static string $resource = CouponResource::class;

    public function getTabs(): array
    {
        $user = auth()->user();

        $reserved = (int) Status::RESERVED->value;
        $scheduled = implode(',', array_map('intval', Status::getScheduledCases()));
        $notBooked = implode(',', array_map('intval', Status::getNotBookedCases()));
        $booked = implode(',', array_map('intval', Status::getBookedCases()));

        $counts = Coupon::query()
            ->selectRaw('COUNT(*) AS allCount')
            ->selectRaw('COUNT(CASE WHEN status IS NULL THEN 1 END) AS notScheduled')
            ->selectRaw("COUNT(CASE WHEN status = $reserved THEN 1 END) AS reserved")
            ->selectRaw("COUNT(CASE WHEN status IN ($scheduled) THEN 1 END) AS scheduled")
            ->selectRaw("COUNT(CASE WHEN status IN ($notBooked) THEN 1 END) AS notBooked")
            ->selectRaw("COUNT(CASE WHEN status IN ($booked) THEN 1 END) AS booked")
            ->first();

        $tabs = [];

        // Only for non–customer service
        if (! $user->hasRoleSlug('customer service')) {
            $tabs['all'] = Tab::make(__('coupon.tabs.all'))
                ->icon('heroicon-o-rectangle-stack')
                ->badge($counts->allCount);
        }

        // Only for customer service
        if ($user->hasRoleSlug('customer service')) {
            $tabs['Not scheduled'] = Tab::make(__('coupon.tabs.not_scheduled'))
                ->icon('heroicon-o-rectangle-stack')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('status'))
                ->badge($counts->notScheduled);

            $tabs['reserved'] = Tab::make(__('coupon.tabs.reserved'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', Status::getReservedCases()))
                ->badge($counts->reserved);
        }

        // Tabs visible to everyone
        $tabs['scheduled'] = Tab::make(__('coupon.tabs.scheduled'))
            ->icon('heroicon-o-clock')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', Status::getScheduledCases()))
            ->badge($counts->scheduled);

        $tabs['not_booked'] = Tab::make(__('coupon.tabs.not_booked'))
            ->icon('heroicon-o-x-circle')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', Status::getNotBookedCases()))
            ->badge($counts->notBooked);

        $tabs['booked'] = Tab::make(__('coupon.tabs.booked'))
            ->icon('heroicon-o-check-circle')
            ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('status', Status::getBookedCases()))
            ->badge($counts->booked);

        return $tabs;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            //
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'scheduled';
    }

    protected function getHeaderActions(): array
    {
        return [

            ExportAction::make()
                ->exporter(CouponExporter::class)
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary'),
        ];
    }
}
