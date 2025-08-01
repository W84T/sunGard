<?php

namespace App;

enum Status: int
{
    case  RESERVED = 0;
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

    public static function options(): array
    {
        return array_reduce(self::cases(), function ($carry, $case) {
            $carry[$case->value] = $case->label();

            return $carry;
        }, []);
    }

    /**
     * Get the English label
     */
    public function label(): string
    {
        return match ($this) {
            self::POSTPONED_BY_CUSTOMER => __('status.postponed_by_customer'),
            self::NO_REPLY_OR_WHATSAPP_SENT => __('status.no_reply_or_whatsapp_sent'),
            self::DUPLICATE_POSTPONED => __('status.duplicate_postponed'),

            self::NOT_INTERESTED => __('status.not_interested'),
            self::MULTIPLE_POSTPONES_OR_AVOID => __('status.multiple_postpones_or_avoid'),
            self::BOOKED => __('status.booked'),
            self::HEADING_TO_BRANCH => __('status.heading_to_branch'),
            self::TRANSFERRED_OUTSIDE_COMPANY => __('status.transferred_outside_company'),
            self::PRICE_INQUIRY => __('status.price_inquiry'),
            self::CUSTOMER_SERVED => __('status.customer_served'),

            self::RESERVED => __('status.reserved'),
        };
    }

    public static function optionsExcept(array $except): array
    {
        return array_reduce(self::cases(), function ($carry, $case) use ($except) {
            if (!in_array($case, $except)) {
                $carry[$case->value] = $case->label();
            }
            return $carry;
        }, []);
    }

    public function isScheduled(): bool
    {
        return in_array($this, self::getScheduledCases());
    }

    public static function getScheduledCases(): array
    {
        return [
            self::POSTPONED_BY_CUSTOMER,
            self::NO_REPLY_OR_WHATSAPP_SENT,
            self::DUPLICATE_POSTPONED,
        ];
    }

    public function isNotBooked(): bool
    {
        return in_array($this, self::getNotBookedCases());
    }

    public static function getNotBookedCases(): array
    {
        return [
            self::NOT_INTERESTED,
            self::MULTIPLE_POSTPONES_OR_AVOID,
        ];
    }

    public function isBooked(): bool
    {
        return in_array($this, self::getBookedCases());
    }

    public static function getBookedCases(): array
    {
        return [
            self::BOOKED,
            self::HEADING_TO_BRANCH,
            self::TRANSFERRED_OUTSIDE_COMPANY,
            self::PRICE_INQUIRY,
            self::CUSTOMER_SERVED,
        ];
    }

    public function isReserved(): bool
    {
        return in_array($this, self::getReservedCases());
    }

    public static function getReservedCases(): array
    {
        return [
            self::RESERVED,
        ];
    }

}
