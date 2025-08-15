<?php

namespace App\Observers;

use App\Filament\Resources\Coupons\CouponResource;
use App\Models\Coupon;
use App\Models\Ticket;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class TicketObserver
{
    /**
     * Handle the Ticket "created" event.
     */
    public function created(Ticket $ticket): void
    {

        $this->sendTicketNotification($ticket);
    }

    private function sendTicketNotification(Ticket $ticket): void
    {
        // Early exit if no coupon
        if (empty($ticket->coupon_id)) {
            return;
        }

        $coupon = Coupon::find($ticket->coupon_id);
        if (! $coupon || empty($coupon->employee_id)) {
            return;
        }

        // Figure out who to notify
        if ($ticket->submitted_to === 'customer service manager') {
            $employee = User::find($coupon->employee_id);
            $manager = $employee?->creator;

            if (! $manager) {
                return;
            }

            Notification::make()
                ->title('استلمت تذكرة جديدة')
                ->body('تم رفع تذكرة جديدة للكوبون'.$coupon->id)
                ->success()
                ->actions([
                    Action::make('edit')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->url(CouponResource::getUrl('edit', ['record' => $coupon->id])),
                    Action::make('markAsUnread')
                        ->markAsUnread(),
                ])
                ->sendToDatabase($manager);

            Log::info("Ticket #{$ticket->id} sent to customer service manager: {$manager->name}");

        }

        //        if ($ticket->submitted_to === 'admin') {
        //            $admin = User::whereHas('roles', fn($q) => $q->where('slug', 'admin'))
        //                ->first();
        //
        //            if (!$admin) {
        //                return;
        //            }
        //
        //            Log::info("Ticket #{$ticket->id} sent to admin: {$admin->name}");
        //            // Notification::send($admin, new TicketCreatedNotification($ticket));
        //        }

    }
}
