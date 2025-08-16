<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TicketPriority: string implements HasLabel
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function getLabel(): string
    {
        return match ($this) {
            self::LOW => __('coupon.ticket.priority_options.low'),
            self::MEDIUM => __('coupon.ticket.priority_options.medium'),
            self::HIGH => __('coupon.ticket.priority_options.high'),
        };
    }
}
