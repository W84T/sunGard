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
    /**
     * Handle the Coupon "updated" event.
     */
    public function updated(Coupon $coupon): void
    {
        $this->sendStatusChangeNotification($coupon);
    }

    private function sendStatusChangeNotification(Coupon $coupon): void
    {
        if (! $coupon->isDirty('status') && ! $coupon->isDirty('is_confirmed')) {
            return;
        }

        $agent = User::find($coupon->agent_id);
        if (! $agent) {
            return;
        }

        $originalStatus = $coupon->getOriginal('status');
        $currentStatus = $coupon->status;
        $originalIsConfirmed = $coupon->getOriginal('is_confirmed');
        $currentIsConfirmed = $coupon->is_confirmed;
        $notification = null;

        // Scenario 1: Scheduled
        if ($originalStatus == Status::RESERVED && in_array($currentStatus, Status::getScheduledCases())) {
            $notification = Notification::make()
                ->title('تم جدولة الكوبون')
                ->body('تم جدولة الكوبون '.$coupon->name)
                ->success()
                ->actions([
                    Action::make('view')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->url(CouponResource::getUrl('edit', ['record' => $coupon->id])),
                    Action::make('markAsUnread')
                        ->markAsUnread(),
                ]);
        }

        // Scenario 2: Confirmed
        if (! is_null($currentStatus) && $currentIsConfirmed === true) {
            $notification = Notification::make()
                ->title('تم تأكيد الكوبون')
                ->body('تم تأكيد الكوبون '.$coupon->name)
                ->success()
                ->actions([
                    Action::make('view')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->url(CouponResource::getUrl('edit', ['record' => $coupon->id])),
                    Action::make('markAsUnread')
                        ->markAsUnread(),
                ]);
        }

        // Scenario 3: Not Booked
        if ($currentStatus && in_array($currentStatus, Status::getNotBookedCases())) {
            $notification = Notification::make()
                ->title('لم يتم حجز الكوبون')
                ->body('لم يتم حجز الكوبون '.$coupon->name)
                ->danger()
                ->actions([
                    Action::make('view')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->url(CouponResource::getUrl('edit', ['record' => $coupon->id])),
                    Action::make('markAsUnread')
                        ->markAsUnread(),
                ]);
        }

        // Scenario 4: Customer Served
        if ($currentStatus === Status::CUSTOMER_SERVED) {
            $notification = Notification::make()
                ->title('تم خدمة العميل')
                ->body('تم خدمة العميل '.$coupon->name)
                ->success()
                ->actions([
                    Action::make('view')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->url(CouponResource::getUrl('edit', ['record' => $coupon->id])),
                    Action::make('markAsUnread')
                        ->markAsUnread(),
                ]);
        }

        if ($notification) {
            $notification->sendToDatabase($agent);
        }
    }
}
