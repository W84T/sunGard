<?php

namespace App\Filament\Resources\Coupons\Resources\Tickets\Schemas;

use App\Enums\TicketPriority;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('created_by')
                    ->label(__('coupon.ticket.created_by'))
                    ->numeric(),
                TextInput::make('closed_by')
                    ->label(__('coupon.ticket.closed_by'))
                    ->numeric(),
                TextInput::make('title')
                    ->label(__('coupon.ticket.title'))
                    ->required(),
                Textarea::make('description')
                    ->label(__('coupon.ticket.description'))
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('coupon.ticket.status'))
                    ->options(fn(): array => [
                        'open' => __('coupon.ticket.status_options.open'),
                        'closed' => __('coupon.ticket.status_options.closed'),
                    ])
                    ->default('open')
                    ->required(),
                Select::make('priority')
                    ->label(__('coupon.ticket.priority'))
                    ->options(TicketPriority::class)
                    ->default(TicketPriority::LOW)
                    ->required(),
                Select::make('submitted_to')
                    ->label(__('coupon.ticket.submitted_to'))
                    ->options(fn(): array => [
                        'admin' => __('coupon.ticket.submitted_to_options.admin'),
                        'customer service manager' => __('coupon.ticket.submitted_to_options.customer_service_manager'),
                    ])
                    ->default('customer service manager')
                    ->required(),
                DateTimePicker::make('closed_at')
                    ->label(__('coupon.ticket.closed_at')),
            ]);
    }
}
