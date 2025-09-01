<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages\CouponRevisions;
use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Filament\Resources\Coupons\Pages\ViewCoupon;
use App\Filament\Resources\Coupons\RelationManagers\TicketsRelationManager;
use App\Filament\Resources\Coupons\Schemas\CouponForm;
use App\Filament\Resources\Coupons\Schemas\CouponInfolist;
use App\Filament\Resources\Coupons\Tables\CouponsTable;
use App\Filament\Resources\Tickets\TicketResource;
use App\Models\Coupon;
use App\Models\User;
use BackedEnum;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

class CouponResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Coupon::class;

    protected static ?int $navigationSort = 5;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Ticket;

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return Phosphor::Ticket->getIconForWeight(PhosphorWeight::Regular);
    }

    public static function getActiveNavigationIcon(): string|BackedEnum|null
    {
        return Phosphor::Ticket->getIconForWeight(PhosphorWeight::Duotone);
    }

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
            TicketsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'view' => ViewCoupon::route('/{record}'),
            'edit' => EditCoupon::route('/{record}/edit'),
            'revisions' => CouponRevisions::route('/{record}/revisions'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        // Customer service (regular) – already handled
        if ($user->hasRoleSlug('customer service')) {
            return $query->where(function ($q) use ($user) {
                $q->where('employee_id', $user->id)
                    ->orWhere(function ($sub) {
                        $sub->whereNull('employee_id')
                            ->whereNull('status');
                    });
            });
        }

        // Agents – already handled
        if ($user->hasRoleSlug('agent')) {
            return $query->where(function ($q) use ($user) {
                $q->where('agent_id', $user->id)
                    ->orWhere(function ($sub) {
                        $sub->whereNull('agent_id')
                            ->whereNull('status');
                    });
            });
        }

        // Marketers – only see coupons of their agents
        if ($user->hasRoleSlug('marketer')) {
            $agentIds = User::where('created_by', $user->id)->pluck('id');

            return $query->whereIn('agent_id', $agentIds);
        }

        // Customer service manager – only see coupons of their customer service employees
        if ($user->hasRoleSlug('customer service manager')) {
            $employeeIds = User::where('created_by', $user->id)->pluck('id');

            return $query->whereIn('employee_id', $employeeIds);
        }

        if ($user->hasRoleSlug('branch manager')) {
            return $query->where('sungard_branch_id', $user->sungard_branch_id);
        }

        // Fallback – admins / others can see everything
        return $query->withoutGlobalScopes([
            SoftDeletingScope::class,
        ]);
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

            'delete',
            'delete_any',

            'force_delete',
            'force_delete_any',

            'submit_ticket',
            'change_status',
            'reserve_coupon',
            'revision'
        ];
    }

    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make()
                ->label(__('coupon.plural'))
                ->icon(static::getNavigationIcon())
                ->url(static::getUrl('index'))
                ->isActiveWhen(fn() => request()->routeIs([
                    'filament.admin.resources.coupons.index',
                    'filament.admin.resources.coupons.edit',
                    'filament.admin.resources.coupons.view',
                ])),
        ];
    }

}
