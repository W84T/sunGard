<?php

namespace App\Filament\Resources\Coupons\Resources\Tickets\Pages;

use App\Filament\Resources\Coupons\Resources\Tickets\TicketResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;
}
