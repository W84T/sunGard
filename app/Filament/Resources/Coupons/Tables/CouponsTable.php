<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Filament\Actions\ChangeStatusAction;
use App\Filament\Actions\ReserveCouponAction;
use App\Filament\Actions\ChangeReservation;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        $user = auth()->user();

        return $table
            ->columns(
                collect([
                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('agent.name')
                        ->label(__('coupon.table.agent_id'))
                        ->numeric()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true) : null,

                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('branchRelation.name')
                        ->label(__('coupon.table.branch_id'))
                        ->numeric()
                        ->sortable()
                        ->toggleable() : null,

                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('exhibitionRelation.name')
                        ->label(__('coupon.table.exhibition_id'))
                        ->numeric()
                        ->sortable()
                        ->toggleable() : null,

                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('employee_id')
                        ->label(__('coupon.table.employee_id'))
                        ->numeric()
                        ->toggleable(isToggledHiddenByDefault: true) : null,

                    TextColumn::make('customer_name')
                        ->label(__('coupon.table.customer_name'))
                        ->searchable()
                        ->toggleable(),

                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('customer_email')
                        ->label(__('coupon.table.customer_email'))
                        ->searchable()
                        ->toggleable() : null,

                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('customer_phone')
                        ->label(__('coupon.table.customer_phone'))
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true) : null,

                    TextColumn::make('car_model')
                        ->label(__('coupon.table.car_model'))
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('car_brand')
                        ->label(__('coupon.table.car_brand'))
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('car_category')
                        ->label(__('coupon.table.car_category'))
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true),

                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('plate_number')
                        ->label(__('coupon.table.plate_number'))
                        ->searchable()
                        ->toggleable(isToggledHiddenByDefault: true) : null,

                    $user->roles->contains('slug', 'admin') || $user->roles->contains('slug', 'employee') ? IconColumn::make('is_confirmed')
                        ->label(__('coupon.table.is_confirmed'))
                        ->boolean()
                        ->toggleable(isToggledHiddenByDefault: true) : null,

//                    $user->roles->contains('slug', 'admin') || $user->roles->contains('slug', 'employee') ? SelectColumn::make('status')
//                        ->options(Status::options())
//                        ->sortable()
//                        ->toggleable(isToggledHiddenByDefault: false)
//                        : null,

                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('reserved_date')
                        ->label(__('coupon.table.reserved_date'))
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true) : null,

                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('reached_at')
                        ->label(__('coupon.table.reached_at'))
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true) : null,

                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('deleted_at')
                        ->label(__('coupon.table.deleted_at'))
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true) : null,

                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('created_at')
                        ->label(__('coupon.table.created_at'))
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true) : null,

                    !$user->roles->contains('slug', 'employee') ? TextColumn::make('updated_at')
                        ->label(__('coupon.table.updated_at'))
                        ->dateTime()
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: true) : null,
                ])
                    ->filter()
                    ->all()
            )
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ReserveCouponAction::make()
                    ->label(__('coupon.actions.reserve_coupon'))
                    ->color('success'),
                ChangeStatusAction::make()
                    ->label(__('coupon.actions.change_status'))
                    ->color('info'),
                ActionGroup::make([
                    ChangeReservation::make()
                        ->label(__('coupon.actions.reserve_date')),
                    ViewAction::make()
                        ->color('gray'),
                    EditAction::make()
                        ->color('primary'),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
//                    ChangeStatusAction::make()->bulk(),
                    DeleteBulkAction::make()
                        ->color('danger'),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
