<?php

namespace App\Filament\Resources\Coupons\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class CouponsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('agent_id')
                    ->label(__('coupon.table.agent_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('branch_id')
                    ->label(__('coupon.table.branch_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('exhibition_id')
                    ->label(__('coupon.table.exhibition_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('employee_id')
                    ->label(__('coupon.table.employee_id'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('customer_name')
                    ->label(__('coupon.table.customer_name'))
                    ->searchable(),
                TextColumn::make('customer_email')
                    ->label(__('coupon.table.customer_email'))
                    ->searchable(),
                TextColumn::make('customer_phone')
                    ->label(__('coupon.table.customer_phone'))
                    ->searchable(),
                TextColumn::make('car_model')
                    ->label(__('coupon.table.car_model'))
                    ->searchable(),
                TextColumn::make('car_brand')
                    ->label(__('coupon.table.car_brand'))
                    ->searchable(),
                TextColumn::make('car_category')
                    ->label(__('coupon.table.car_category'))
                    ->searchable(),
                TextColumn::make('plate_number')
                    ->label(__('coupon.table.plate_number'))
                    ->searchable(),
                IconColumn::make('is_confirmed')
                    ->label(__('coupon.table.is_confirmed'))
                    ->boolean(),
                TextColumn::make('status')
                    ->label(__('coupon.table.status'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('reserved_date')
                    ->label(__('coupon.table.reserved_date'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('reached_at')
                    ->label(__('coupon.table.reached_at'))
                    ->dateTime()
                    ->sortable(),
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
