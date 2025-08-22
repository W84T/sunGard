<?php

namespace App\Filament\Resources\Branches\RelationManagers;

use App\Filament\Resources\Coupons\CouponResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CouponRelationManager extends RelationManager
{
    protected static string $relationship = 'coupon';

    protected static ?string $relatedResource = CouponResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();

                // reuse your role-based visibility logic
                if ($user->hasRoleSlug('customer service')) {
                    $query->where(function ($q) use ($user) {
                        $q->where('employee_id', $user->id)
                            ->orWhere(function ($sub) {
                                $sub->whereNull('employee_id')
                                    ->whereNull('status');
                            });
                    });
                }

                if ($user->hasRoleSlug('agent')) {
                    $query->where(function ($q) use ($user) {
                        $q->where('agent_id', $user->id)
                            ->orWhere(function ($sub) {
                                $sub->whereNull('agent_id')
                                    ->whereNull('status');
                            });
                    });
                }

                if ($user->hasRoleSlug('marketer')) {
                    $agentIds = User::where('created_by', $user->id)->pluck('id');
                    $query->whereIn('agent_id', $agentIds);
                }

                if ($user->hasRoleSlug('customer service manager')) {
                    $employeeIds = User::where('created_by', $user->id)->pluck('id');
                    $query->whereIn('employee_id', $employeeIds);
                }

                if ($user->hasRoleSlug('branch manager')) {
                    $query->where('sungard_branch_id', $user->sungard_branch_id);
                }

                // admins / fallback – no restriction
            });
    }
}
