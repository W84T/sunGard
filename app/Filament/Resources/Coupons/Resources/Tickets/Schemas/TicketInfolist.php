<?php

namespace App\Filament\Resources\Coupons\Resources\Tickets\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('created_by')
                    ->label(__('coupon.ticket.created_by'))
                    ->numeric(),
                TextEntry::make('closed_by')
                    ->label(__('coupon.ticket.closed_by'))
                    ->numeric(),
                TextEntry::make('title')
                    ->label(__('coupon.ticket.title')),
                TextEntry::make('status')
                    ->label(__('coupon.ticket.status')),
                TextEntry::make('priority')
                    ->label(__('coupon.ticket.priority'))
                    ->badge(),
                TextEntry::make('submitted_to')
                    ->label(__('coupon.ticket.submitted_to')),
                TextEntry::make('closed_at')
                    ->label(__('coupon.ticket.closed_at'))
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->label(__('coupon.ticket.created_at'))
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->label(__('coupon.ticket.updated_at'))
                    ->dateTime(),
            ]);
    }
}
