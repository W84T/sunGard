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
                        ->grow(false)
                ])
                    ->columnSpan('full'),

                Group::make()
                    ->columnSpan(2)
                    ->schema([
                        Section::make(__('coupon.infolist.customer_information'))
                            ->icon(Heroicon::User)
                            ->collapsed(false)
                            ->schema([
                                Group::make()
                                    ->schema([
                                        TextEntry::make('customer_name')
                                            ->label(__('coupon.infolist.customer_name'))
                                            ->size(TextSize::Large),
                                        TextEntry::make('customer_email')
                                            ->label(__('coupon.infolist.email'))
                                            ->size(TextSize::Large),
                                        TextEntry::make('customer_phone')
                                            ->label(__('coupon.infolist.phone'))
                                            ->size(TextSize::Large),
                                    ])
                                    ->columns(3),

                                Group::make()
                                    ->schema([
                                        Group::make()
                                            ->columnSpan(2)
                                            ->schema([
                                                TextEntry::make('car_brand')
                                                    ->label(__('coupon.infolist.car_brand'))
                                                    ->size(TextSize::Large),
                                                TextEntry::make('car_model')
                                                    ->label(__('coupon.infolist.car_model'))
                                                    ->size(TextSize::Large),
                                                TextEntry::make('car_category')
                                                    ->label(__('coupon.infolist.car_category'))
                                                    ->size(TextSize::Large),
                                                TextEntry::make('car_plate')
                                                    ->label(__('coupon.infolist.car_plate'))
                                                    ->size(TextSize::Large),
                                            ])
                                            ->columns(2),
                                    ])
                                    ->columns(3),
                            ]),

                        Section::make()
                            ->schema([

//                                ImageEntry::make('coupon_link'),
                                FilePreview::make('coupon_link')
                                    ->label(__('coupon.infolist.coupon_image')),

//                                ViewEntry::make('coupon_link')
//                                    ->label('Coupon Image')
//                                    ->view('infolists.components.image-with-preview')
//                                    ->state(fn ($record) => $record->coupon_link) // important to resolve it
//                                    ->columnSpan(2)
                            ])
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
                            ->schema([
                                TextEntry::make('employee.name')
                                    ->size(TextSize::Large)
                                    ->label(__('coupon.infolist.employee_name')),

                                TextEntry::make('reached_at')
                                    ->label(__('coupon.infolist.reached_at'))
                                    ->date()
                                    ->visible(fn($record) => filled($record->reached_at)),

                                TextEntry::make('separator')
                                    ->html()
                                    ->hiddenLabel()
                                    ->state('<hr>'),

                                TextEntry::make('reserved_date')
                                    ->label(__('coupon.infolist.reservation_date'))
                                    ->date()
                                    ->visible(fn($record) => filled($record->reserved_date)),

                                TextEntry::make('sungard_branch_id.name')
                                    ->size(TextSize::Large)
                                    ->visible(fn($record) => filled($record->sungard_branch_id))
                                    ->label(__('coupon.infolist.sungard_branch_name')),

                            ]),
                    ]),
            ]);
    }


//
//return $schema->columns(2)->components([
//
//Section::make('General Information')
//->schema([

//
//ImageEntry::make('coupon_link')->label('Coupon Image'),
//
//TextEntry::make('is_confirmed')
//    ->label('Is Confirmed')
//    ->formatStateUsing(fn(bool $state) => $state ? 'Yes' : 'No'),
//
//                    TextEntry::make('reserved_date')->label('Reserved Date')->dateTime(),
//                    TextEntry::make('reached_at')->label('Reached At')->dateTime(),
//                ]),
//
//
//
//            Section::make('Staff & Branch Info')
//                ->schema([
//                    TextEntry::make('agent.name')->label('Agent Name'),
//                    TextEntry::make('branchRelation.name')->label('Branch Name'),
//                    TextEntry::make('exhibitionRelation.name')->label('Exhibition Name'),
//                    TextEntry::make('employee.name')->label('Employee Name'),
//                ])
//                ->columns(2),
//
//            Section::make('Timestamps')
//                ->schema([
//                    TextEntry::make('created_at')->label('Created At')->dateTime(),
//                    TextEntry::make('updated_at')->label('Updated At')->dateTime(),
//                ])
//                ->columns(2),
//        ]);
}
