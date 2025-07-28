<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CouponInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('customer_name'),
                TextEntry::make('customer_email'),
                TextEntry::make('customer_phone'),
                TextEntry::make('coupon_link'),
                TextEntry::make('car_model'),
                TextEntry::make('car_brand'),
                TextEntry::make('car_category'),
                TextEntry::make('plate_number'),
                TextEntry::make('is_confirmed')
                    ->formatStateUsing(fn(bool $state) => $state ? 'Yes' : 'No'),
                TextEntry::make('status'),
                TextEntry::make('reserved_date')
                    ->dateTime(),
                TextEntry::make('reached_at')
                    ->dateTime(),
                TextEntry::make('agent.name')
                    ->label('Agent Name'),
                TextEntry::make(
                    'branchRelation.name')
                    ->label('Branch Name'),
                TextEntry::make(
                    'exhibitionRelation.name')
                    ->label('Exhibition Name'),
                TextEntry::make('employee.name')
                    ->label('Employee Name'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
