<?php

namespace App\Filament\Resources\Coupons\Resources\Tickets;

use App\Filament\Resources\Coupons\CouponResource;
use App\Filament\Resources\Coupons\Resources\Tickets\Pages\CreateTicket;
use App\Filament\Resources\Coupons\Resources\Tickets\Pages\EditTicket;
use App\Filament\Resources\Coupons\Resources\Tickets\Pages\ViewTicket;
use App\Filament\Resources\Coupons\Resources\Tickets\Schemas\TicketForm;
use App\Filament\Resources\Coupons\Resources\Tickets\Schemas\TicketInfolist;
use App\Filament\Resources\Coupons\Resources\Tickets\Tables\TicketsTable;
use App\Models\Ticket;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = CouponResource::class;

    public static function getModelLabel(): string
    {
        return __('coupon.ticket.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('coupon.ticket.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return TicketForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TicketInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TicketsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'view' => ViewTicket::route('/{record}'),
        ];
    }
}
