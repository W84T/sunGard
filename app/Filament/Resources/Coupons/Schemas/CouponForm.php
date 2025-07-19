<?php

namespace App\Filament\Resources\Coupons\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class CouponForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                // Left 2/3
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make('Customer Information')
                            ->columns(2)
                            ->schema([
                                TextInput::make('customer_name')
                                    ->required(),

                                TextInput::make('customer_phone')
                                    ->tel()
                                    ->required(),

                                TextInput::make('customer_email')
                                    ->email()
                                    ->columnSpan(2),
                            ]),

                        Section::make('Vehicle Information')
                            ->columns(2)
                            ->schema([
                                TextInput::make('car_model')
                                    ->required(),

                                TextInput::make('plate_number')
                                    ->required(),

                                TextInput::make('car_brand'),

                                TextInput::make('car_category'),
                            ]),
                    ]),

                // Right 1/3 (Sidebar)
                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make('Coupon & Assignment')
                            ->columns(1)
                            ->schema([
                                Select::make('agent_id')
                                    ->relationship(
                                        name: 'agent',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->whereHas('roles', fn (Builder $query) => $query->where('name', 'agent'))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Agent'),

                                Select::make('employee_id')
                                    ->relationship(
                                        name: 'employee',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->whereHas('roles', fn (Builder $query) => $query->where('name', 'employee'))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label('Customer Service'),

                                Select::make('branch_id')
                                    ->relationship('branchRelation', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('exhibition_id')
                                    ->relationship('exhibitionRelation', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                        Section::make('Status & Reservation')
                            ->columns(1)
                            ->schema([
                                Toggle::make('is_confirmed')
                                    ->required()
                                    ->label('Confirmed'),

                                TextInput::make('status')
                                    ->required()
                                    ->numeric()
                                    ->default(1),

                                DateTimePicker::make('reserved_date')
                                    ->label('Reserved Date'),

                                DateTimePicker::make('reached_at')
                                    ->label('Reached At'),
                            ]),
                    ]),
            ]);
    }
}
