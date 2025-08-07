<?php

namespace App\Observers;

use App\Models\Coupon;
use App\Models\Ticket;
use App\Models\User;
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
        if (
            $ticket->submitted_to === 'customer service manager' &&
            $ticket->coupon_id
        ) {
            $coupon = Coupon::find($ticket->coupon_id);

            if (!$coupon || !$coupon->employee_id) {
                return;
            }

            $employee = User::find($coupon->employee_id);
            $manager = $employee?->createdBy;

            if ($manager) {
                // Notify the manager
                // e.g., Notification::send($manager, new TicketCreatedNotification($ticket));
                Log::info("Ticket #{$ticket->id} sent to manager: {$manager->name}");
            }
        }
    }
}
