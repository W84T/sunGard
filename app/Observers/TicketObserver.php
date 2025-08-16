<?php

namespace App\Observers;

use App\Enums\TicketPriority;
use App\Filament\Resources\Coupons\CouponResource;
use App\Models\Coupon;
use App\Models\Ticket;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Schmeits\FilamentPhosphorIcons\Support\Icons\Phosphor;
use Schmeits\FilamentPhosphorIcons\Support\Icons\PhosphorWeight;

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
        if (empty($ticket->coupon_id)) {
            return;
        }

        $coupon = Coupon::find($ticket->coupon_id);
        if (!$coupon || empty($coupon->employee_id)) {
            return;
        }

        // Map priorities to icons and colors
        $priorityMap = [
            TicketPriority::LOW->value => ['icon' => Phosphor::Info->getIconForWeight(PhosphorWeight::Duotone), 'color' => 'info'],
            TicketPriority::MEDIUM->value => ['icon' => Phosphor::Warning->getIconForWeight(PhosphorWeight::Duotone), 'color' => 'warning'],
            TicketPriority::HIGH->value => ['icon' => Phosphor::Radioactive->getIconForWeight(PhosphorWeight::Duotone), 'color' => 'danger'],
        ];

        $settings = $priorityMap[$ticket->priority->value] ?? $priorityMap[TicketPriority::LOW->value];

        if ($ticket->submitted_to === 'customer service manager') {
            $employee = User::find($coupon->employee_id);
            $manager = $employee?->creator;

            if (!$manager) {
                return;
            }

            Notification::make()
                ->title($ticket->title)
                ->body("تم رفع تذكرة جديدة للكوبون #{$coupon->id} (الأولوية: {$ticket->priority->getLabel()})")
                ->icon($settings['icon'])
                ->iconColor($settings['color'])
                ->color($settings['color'])
                ->actions([
                    Action::make('edit')
                        ->label('عرض')
                        ->icon('heroicon-m-eye')
                        ->color('info')
                        ->url(CouponResource::getUrl('edit', ['record' => $coupon->id])),
                    Action::make('markAsRead')
                        ->label('وضع علامة كمقروء')
                        ->markAsRead(),
                ])
                ->sendToDatabase($manager);

            Log::info("Ticket #{$ticket->id} (priority {$ticket->priority->value}) sent to {$manager->name}");
        }
    }

}
