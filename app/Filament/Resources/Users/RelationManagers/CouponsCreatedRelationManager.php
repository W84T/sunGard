<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Coupons\CouponResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CouponsCreatedRelationManager extends RelationManager
{
    protected static string $relationship = 'couponsCreated';

    protected static ?string $relatedResource = CouponResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {

        if ($ownerRecord->hasRoleSlug('agent')) {
            return true;
        }

        return false;
    }
}
