<?php

namespace App;

enum Status: int
{
    case POSTPONED_BY_CUSTOMER = 1;
    case NO_REPLY_OR_WHATSAPP_SENT = 2;
    case DUPLICATE_POSTPONED = 3;
    case NOT_INTERESTED = 4;
    case MULTIPLE_POSTPONES_OR_AVOID = 5;
    case BOOKED = 6;
    case HEADING_TO_BRANCH = 7;
    case TRANSFERRED_OUTSIDE_COMPANY = 8;
    case PRICE_INQUIRY = 9;
    case CUSTOMER_SERVED = 10;

    /**
     * Get the English label
     */
    public function label(): string
    {
        return match ($this) {
            self::POSTPONED_BY_CUSTOMER => 'Postponed by Customer',
            self::NO_REPLY_OR_WHATSAPP_SENT => 'No Reply or WhatsApp Sent',
            self::DUPLICATE_POSTPONED => 'Duplicate Postponed',
            self::NOT_INTERESTED => 'Not Interested',
            self::MULTIPLE_POSTPONES_OR_AVOID => 'Multiple Postpones / Avoiding Booking',
            self::BOOKED => 'Booked',
            self::HEADING_TO_BRANCH => 'Heading to Branch',
            self::TRANSFERRED_OUTSIDE_COMPANY => 'Transferred Outside Company',
            self::PRICE_INQUIRY => 'Price Inquiry',
            self::CUSTOMER_SERVED => 'Customer Served',
        };
    }

    public static function options(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->label();
            return $carry;
        }, []);
    }
}
