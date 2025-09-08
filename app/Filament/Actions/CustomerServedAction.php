<?php

namespace App\Filament\Actions;

use App\Models\Coupon;
use App\Status;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Size;
use Illuminate\Support\Facades\Auth;

class CustomerServedAction
{
    public static function make(): Action
    {
        return Action::make('customer_served')
            ->label(__('coupon.actions.customer_served'))
            ->color('success')
            ->requiresConfirmation()
            ->modalWidth('4xl')
            ->modalHeading(__('coupon.actions.customer_served_confirm_heading'))
            ->visible(fn (?Coupon $record) =>
                Auth::user()?->hasRoleSlug('branch manager')
                && $record?->status !== Status::CUSTOMER_SERVED
            )
            ->schema([
                Forms\Components\RichEditor::make('note')
                    ->label(__('coupon.form.note'))
                    ->required()
                    ->maxLength(500),
            ])
            ->action(function (array $data, Coupon $record): void {
                $record->update([
                    'status' => Status::CUSTOMER_SERVED,
                    'note'   => $data['note'] ?? null,
                ]);

                if ($data['notify_customer']) {
                    // your notification logic here
                }

                Notification::make()
                    ->title(__('coupon.notification.customer_served_set.title'))
                    ->success()
                    ->send();
            });
    }
}
