<?php

namespace App\Filament\Resources\Branches\RelationManagers;

use App\Filament\Resources\Coupons\CouponResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class CouponRelationManager extends RelationManager
{
    protected static string $relationship = 'coupon';

    protected static ?string $relatedResource = CouponResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
