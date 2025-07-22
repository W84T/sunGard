<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Status;
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
        $user = auth()->user();

        return $schema
            ->columns(3)
            ->components([
                // Left 2/3
                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make(__('coupon.form.customer_information'))
                            ->columns(2)
                            ->schema([
                                TextInput::make('customer_name')
                                    ->label(__('coupon.form.customer_name'))
                                    ->required(),

                                TextInput::make('customer_phone')
                                    ->label(__('coupon.form.customer_phone'))
                                    ->tel()
                                    ->required(),

                                TextInput::make('customer_email')
                                    ->label(__('coupon.form.customer_email'))
                                    ->email()
                                    ->columnSpan(2),
                            ]),

                        Section::make(__('coupon.form.vehicle_information'))
                            ->columns(2)
                            ->schema([
                                TextInput::make('car_model')
                                    ->label(__('coupon.form.car_model'))
                                    ->required(),

                                TextInput::make('plate_number')
                                    ->label(__('coupon.form.plate_number'))
                                    ->required(),

                                TextInput::make('car_brand')
                                    ->label(__('coupon.form.car_brand')),

                                TextInput::make('car_category')
                                    ->label(__('coupon.form.car_category')),
                            ]),
                    ]),

                // Right 1/3 (Sidebar)
                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make(__('coupon.form.coupon_assignment'))
                            ->columns(1)
                            ->schema([
                                Select::make('agent_id')
                                    ->relationship(
                                        name: 'agent',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->whereHas('roles', fn (Builder $query) => $query->where('name', 'agent'))
                                    )
                                    ->visible($user->roles->contains('slug', 'admin') || $user->roles->contains('slug', 'employee'))
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->label(__('coupon.form.agent')),

                                Select::make('employee_id')
                                    ->relationship(
                                        name: 'employee',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->whereHas('roles', fn (Builder $query) => $query->where('name', 'employee'))
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->visible($user->roles->contains('slug', 'admin') || $user->roles->contains('slug', 'employee'))
                                    ->label(__('coupon.form.customer_service')),

                                Select::make('branch_id')
                                    ->relationship('branchRelation', 'name')
                                    ->label(__('coupon.form.branch'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),

                                Select::make('exhibition_id')
                                    ->relationship('exhibitionRelation', 'name')
                                    ->label(__('coupon.form.exhibition'))
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                            ]),
                        Section::make(__('coupon.form.status_reservation'))
                            ->columns(1)
                            ->schema([
                                Toggle::make('is_confirmed')
                                    ->required()
                                    ->label(__('coupon.form.confirmed')),

                                Select::make('status')
                                    ->options(Status::options())
                                    ->searchable()
                                    ->default(1)
                                    ->label(__('coupon.form.status')),

                                DateTimePicker::make('reserved_date')
                                    ->label(__('coupon.form.reserved_date')),

                                DateTimePicker::make('reached_at')
                                    ->label(__('coupon.form.reached_at')),
                            ])->visible($user->roles->contains('slug', 'admin') || $user->roles->contains('slug', 'employee')),
                    ]),
            ]);
    }
}
