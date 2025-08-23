<?php

namespace App\Filament\Actions;

use App\Models\Coupon;
use App\Status;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class CustomerServedAction
{
    public static function make(): Action
    {
        return Action::make('customer_served')
            ->label(__('coupon.actions.customer_served'))
            // ->icon('heroicon-o-user-check')
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading(__('coupon.actions.customer_served_confirm_heading'))
            ->visible(fn (?Coupon $record) =>
                Auth::user()?->hasRoleSlug('branch manager')
                && $record?->status !== Status::CUSTOMER_SERVED
            )
            ->action(function (Coupon $record): void {
                $record->update(['status' => Status::CUSTOMER_SERVED]);

                Notification::make()
                    ->title(__('coupon.notification.customer_served_set.title'))
                    ->success()
                    ->send();
            });
    }
}
