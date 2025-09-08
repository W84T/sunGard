<?php

namespace App\Filament\Resources\Coupons\Schemas;

use App\Filament\Infolists\Components\FilePreview;
use App\Status;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Support\Icons\Heroicon;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;

class CouponInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(3)
            ->components([
                Flex::make([
                    TextEntry::make('is_confirmed')
                        ->hiddenLabel()
                        ->badge()
                        ->size(TextSize::Large)
                        ->weight(FontWeight::Black)
                        ->color(fn($state) => $state ? 'success' : 'danger')
                        ->formatStateUsing(fn($state) => $state ? __('coupon.infolist.confirmed') : __('coupon.infolist.not_confirmed'))
                        ->grow(false),

                    TextEntry::make('status')
                        ->hiddenLabel()
                        ->badge()
                        ->size(TextSize::Large)
                        ->weight(FontWeight::Black)
                        ->formatStateUsing(fn($state) => ($state instanceof Status ? $state : Status::from($state))->label())
                        ->color(fn($state) => match (true) {
                            ($state instanceof Status ? $state : Status::from($state))->isBooked() => 'success',
                            ($state instanceof Status ? $state : Status::from($state))->isScheduled() => 'warning',
                            ($state instanceof Status ? $state : Status::from($state))->isNotBooked() => 'danger',
                            default => 'gray',
                        })
                        ->grow(false),
                ])
                    ->columnSpan('full'),

                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make(__('coupon.infolist.customer_information'))
                            ->icon(Heroicon::User)
                            ->collapsed(false)
                            ->extraAttributes(['class' => 'print:break-after-page'])
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextEntry::make('customer_name')
                                            ->label(__('coupon.infolist.customer_name'))
                                            ->size(TextSize::Large),
                                        TextEntry::make('customer_email')
                                            ->label(__('coupon.infolist.email'))
                                            ->size(TextSize::Large)
                                            ->visible(fn($record) => filled($record->customer_email)),
                                        PhoneEntry::make('customer_phone')
                                            ->label(__('coupon.infolist.phone'))
                                            ->size(TextSize::Large),
                                    ])
                                    ->columns(3),

                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->columnSpanFull()
                                            ->schema([
                                                TextEntry::make('car_brand')
                                                    ->label(__('coupon.infolist.car_brand'))
                                                    ->size(TextSize::Large),
                                                TextEntry::make('car_type')
                                                    ->label(__('coupon.infolist.car_type'))
                                                    ->size(TextSize::Large),
                                                TextEntry::make('car_model')
                                                    ->label(__('coupon.infolist.car_model'))
                                                    ->size(TextSize::Large),
                                                TextEntry::make('car_category')
                                                    ->label(__('coupon.infolist.car_category'))
                                                    ->size(TextSize::Large)
                                                    ->formatStateUsing(fn(string $state) => __('car_categories.' .
                                                        $state)),
                                                TextEntry::make('car_plate')
                                                    ->label(__('coupon.infolist.car_plate'))
                                                    ->size(TextSize::Large),
                                            ])
                                            ->columns(5),
                                    ])
                                    ->columns(3),
                            ]),

                        Section::make()
                            ->extraAttributes(['class' => 'print:break-after-page'])
                            ->schema([
                                FilePreview::make('coupon_link')
                                    ->label(__('coupon.infolist.coupon_image')),
                            ]),
                    ]),

                Group::make()
                    ->columnSpan(1)
                    ->schema([
                        Section::make(__('coupon.infolist.branch_info'))
                            ->icon(Heroicon::Link)
                            ->collapsed(false)
                            ->schema([
                                TextEntry::make('agent.name')
                                    ->size(TextSize::Large)
                                    ->label(__('coupon.infolist.agent_name')),
                                TextEntry::make('branchRelation.name')
                                    ->size(TextSize::Large)
                                    ->label(__('coupon.infolist.branch_name')),
                                TextEntry::make('exhibitionRelation.name')
                                    ->size(TextSize::Large)
                                    ->label(__('coupon.infolist.exhibition_name')),

                            ]),
                        Section::make(__('coupon.infolist.employee'))
                            ->icon(Heroicon::Link)
                            ->collapsed(false)
                            ->visible(fn($record) => filled($record->employee_id))
                            ->schema([

                                TextEntry::make('employee.name')
                                    ->size(TextSize::Large)
                                    ->label(__('coupon.infolist.employee_name')),

                                TextEntry::make('reached_at')
                                    ->label(__('coupon.infolist.reached_at'))
                                    ->dateTime('d/m/Y H:i')
                                    ->visible(fn($record) => filled($record->reached_at)),

                                // Separator appears only if all are filled
                                TextEntry::make('separator')
                                    ->html()
                                    ->hiddenLabel()
                                    ->state('<hr>')
                                    ->visible(fn($record) => filled($record->reached_at)
                                        && filled($record->employee_id)
                                        && filled($record->reserved_date)),

                                TextEntry::make('reserved_date')
                                    ->label(__('coupon.infolist.reservation_date'))
                                    ->dateTime('d/m/Y H:i')
                                    ->visible(fn($record) => filled($record->reserved_date)),

                                TextEntry::make('sungard.name')
                                    ->size(TextSize::Large)
                                    ->label(__('coupon.infolist.sungard_branch_name'))
                                    ->visible(fn($record) => filled($record->sungard_branch_id)),

                                TextEntry::make('note')
                                    ->label(__('coupon.infolist.note'))
                                    ->html()
                                    ->visible(fn($record) => filled($record->note)),

                            ]),
                    ]),
            ]);
    }

}
