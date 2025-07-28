<?php

namespace App\Filament\Resources\Coupons\Pages;

use App\Filament\Actions\ChangeStatusAction;
use App\Filament\Resources\Coupons\CouponResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewCoupon extends ViewRecord
{
    protected static string $resource = CouponResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChangeStatusAction::make()
                ->color('info'),
            EditAction::make()
                ->color('primary'),
        ];
    }
}
