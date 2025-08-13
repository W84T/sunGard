<?php

namespace App\Filament\Resources\Coupons\Pages;


use App\Filament\Resources\Coupons\CouponResource;
use Mansoor\FilamentVersionable\RevisionsPage;
class CouponRevisions extends RevisionsPage
{
    protected static string $resource = CouponResource::class;
}
