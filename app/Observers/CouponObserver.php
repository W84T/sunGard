<?php

namespace App\Observers;

use App\Filament\Resources\Coupons\CouponResource;
use App\Models\Coupon;
use App\Models\User;
use App\Status;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CouponObserver
{
    public function updated(Coupon $coupon): void
    {
        $this->sendStatusChangeNotification($coupon);
    }

    private function sendStatusChangeNotification(Coupon $coupon): void
    {
        // In "updated", use wasChanged() (not isDirty()).
        $statusChanged    = $coupon->wasChanged('status');
        $confirmedChanged = $coupon->wasChanged('is_confirmed');

        if (! $statusChanged && ! $confirmedChanged) {
            return;
        }

        $agent = User::find($coupon->agent_id);
        if (! $agent) {
            return;
        }

        // Normalize types to Enum (handles null/int/Enum transparently)
        $originalStatus = self::toEnum($coupon->getOriginal('status'));
        $currentStatus  = self::toEnum($coupon->status);

        $currentIsConfirmed = (bool) $coupon->is_confirmed;

        $notification = null;

        // Priority of notifications:
        // 1) Customer served (final state)
        if ($statusChanged && $currentStatus === Status::CUSTOMER_SERVED) {
            $notification = $this->makeNotification(
                'تم خدمة العميل',
                'تم خدمة العميل ' . $coupon->name,
                'success',
                $coupon
            );
        }
        // 2) Confirmed (manager action)
        elseif ($confirmedChanged && $currentIsConfirmed === true) {
            $notification = $this->makeNotification(
                'تم تأكيد الكوبون',
                'تم تأكيد الكوبون ' . $coupon->name,
                'success',
                $coupon
            );
        }
        // 3) Scheduled (from RESERVED -> any scheduled status)
        elseif ($statusChanged
            && $originalStatus === Status::RESERVED
            && $currentStatus?->isScheduled()
        ) {
            $notification = $this->makeNotification(
                'تم جدولة الكوبون',
                'تم جدولة الكوبون ' . $coupon->name,
                'success',
                $coupon
            );
        }
        // 4) Booked (but not CUSTOMER_SERVED which is handled above)
        elseif ($statusChanged && $currentStatus?->isBooked()) {
            $notification = $this->makeNotification(
                'تم حجز الكوبون',
                'تم حجز الكوبون ' . $coupon->name,
                'success',
                $coupon
            );
        }
        // 5) Not booked
        elseif ($statusChanged && $currentStatus?->isNotBooked()) {
            $notification = $this->makeNotification(
                'لم يتم حجز الكوبون',
                'لم يتم حجز الكوبون ' . $coupon->name,
                'danger',
                $coupon
            );
        }

        if ($notification) {
            $notification->sendToDatabase($agent);
        }
    }

    /**
     * Coerce mixed value to Status enum (or null).
     *
     * @param  Status|int|string|null  $value
     */
    private static function toEnum($value): ?Status
    {
        if ($value === null) {
            return null;
        }

        return $value instanceof Status ? $value : Status::from((int) $value);
    }

    /**
     * Small helper to keep notification creation DRY.
     */
    private function makeNotification(string $title, string $body, string $tone, Coupon $coupon): Notification
    {
        $n = Notification::make()
            ->title($title)
            ->body($body)
            ->actions([
                Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->url(CouponResource::getUrl('view', ['record' => $coupon->id])),
                Action::make('markAsUnread')->markAsUnread(),
            ]);

        // Apply tone
        return match ($tone) {
            'success' => $n->success(),
            'danger'  => $n->danger(),
            'warning' => $n->warning(),
            default   => $n->info(),
        };
    }
}
