<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Filament\Resources\Coupons\Pages\ViewCoupon;
use App\Filament\Resources\Coupons\Schemas\CouponForm;
use App\Filament\Resources\Coupons\Schemas\CouponInfolist;
use App\Filament\Resources\Coupons\Tables\CouponsTable;
use App\Models\Coupon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Ticket;

    public static function getModelLabel(): string
    {
        return __('coupon.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('coupon.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return CouponForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CouponInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CouponsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'view' => ViewCoupon::route('/{record}'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        if ($user->roles->contains('slug', 'agent')) {
            return $query->where('agent_id', $user->id)
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]);
        }

        if ($user->roles->contains('slug', 'marketer')) {
            $agentIds = \App\Models\User::where('created_by', $user->id)->pluck('id');

            return $query->whereIn('agent_id', $agentIds)
                ->withoutGlobalScopes([SoftDeletingScope::class]);
        }

        if ($user->roles->contains('slug', 'employee')) {
            return $query->where('employee_id', $user->id)
                ->withoutGlobalScopes([
                    SoftDeletingScope::class,
                ]);
        }

        return $query->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);

        //        return parent::getEloquentQuery()

    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'restore',
            'restore_any',
            'replicate',
            'reorder',
            'delete',
            'delete_any',
            'force_delete',
            'force_delete_any',
        ];
    }
}
