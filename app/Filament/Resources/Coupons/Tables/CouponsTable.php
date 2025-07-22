<?php

namespace App\Filament\Resources\Coupons\Tables;

use App\Status;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SelectColumn;
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
                TextColumn::make('agent.name')
                    ->label(__('coupon.table.agent_id'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault:  true),

                TextColumn::make('branchRelation.name')
                    ->label(__('coupon.table.branch_id'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('exhibitionRelation.name')
                    ->label(__('coupon.table.exhibition_id'))
                    ->numeric()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('employee_id')
                    ->label(__('coupon.table.employee_id'))
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault:  true),

                    TextColumn::make('customer_name')
                    ->label(__('coupon.table.customer_name'))
                    ->searchable()
                    ->toggleable(),

                    TextColumn::make('customer_email')
                    ->label(__('coupon.table.customer_email'))
                    ->searchable()
                    ->toggleable(),

                    TextColumn::make('customer_phone')
                    ->label(__('coupon.table.customer_phone'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault:  true),

                    TextColumn::make('car_model')
                    ->label(__('coupon.table.car_model'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault:  true),

                    TextColumn::make('car_brand')
                    ->label(__('coupon.table.car_brand'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault:  true),

                    TextColumn::make('car_category')
                    ->label(__('coupon.table.car_category'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault:  true),

                    TextColumn::make('plate_number')
                    ->label(__('coupon.table.plate_number'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault:  true),

                    $user->roles->contains('slug', 'admin') || $user->roles->contains('slug', 'employee') ? IconColumn::make('is_confirmed')
                    ->label(__('coupon.table.is_confirmed'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault:  true) : null,


                    $user->roles->contains('slug', 'admin') || $user->roles->contains('slug', 'employee') ? SelectColumn::make('status')
                    ->options(Status::options())
                    ->sortable()
                        ->toggleable(isToggledHiddenByDefault: false)
                        : null,

                TextColumn::make('reserved_date')
                    ->label(__('coupon.table.reserved_date'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault:  true),

                    TextColumn::make('reached_at')
                    ->label(__('coupon.table.reached_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault:  true),

                    TextColumn::make('deleted_at')
                    ->label(__('coupon.table.deleted_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('created_at')
                    ->label(__('coupon.table.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                    TextColumn::make('updated_at')
                    ->label(__('coupon.table.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                ])
                    ->filter()
                    ->all()
            )
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
