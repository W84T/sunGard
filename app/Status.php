<?php

namespace App;

enum Status: int
{
    case RESERVED = 0;
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
        // [int => "Label"]
        return array_reduce(self::cases(), function (array $carry, self $case) {
            $carry[$case->value] = $case->label();

            return $carry;
        }, []);
    }

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

    /** Same as options() but excluding some statuses (accepts Status|int). */
    public static function optionsExcept(array $except): array
    {
        $exceptValues = array_map(
            fn ($x) => $x instanceof self ? $x->value : (int) $x,
            $except
        );

        return array_reduce(self::cases(), function (array $carry, self $case) use ($exceptValues) {
            if (! in_array($case->value, $exceptValues, true)) {
                $carry[$case->value] = $case->label();
            }

            return $carry;
        }, []);
    }

    /** Int arrays for DB queries (no enum instances). */
    public static function getReservedCases(): array
    {
        return [self::RESERVED->value];
    }

    public static function getScheduledCases(): array
    {
        return [
            self::POSTPONED_BY_CUSTOMER->value,
            self::NO_REPLY_OR_WHATSAPP_SENT->value,
            self::DUPLICATE_POSTPONED->value,
        ];
    }

    public static function getNotBookedCases(): array
    {
        return [
            self::NOT_INTERESTED->value,
            self::MULTIPLE_POSTPONES_OR_AVOID->value,
        ];
    }

    public static function getBookedCases(): array
    {
        return [
            self::BOOKED->value,
            self::HEADING_TO_BRANCH->value,
            self::TRANSFERRED_OUTSIDE_COMPANY->value,
            self::PRICE_INQUIRY->value,
            self::CUSTOMER_SERVED->value,
        ];
    }

    /** Convenience checkers using ints. */
    public function isReserved(): bool
    {
        return in_array($this->value, self::getReservedCases(), true);
    }

    public function isScheduled(): bool
    {
        return in_array($this->value, self::getScheduledCases(), true);
    }

    public function isNotBooked(): bool
    {
        return in_array($this->value, self::getNotBookedCases(), true);
    }

    public function isBooked(): bool
    {
        return in_array($this->value, self::getBookedCases(), true);
    }
}
