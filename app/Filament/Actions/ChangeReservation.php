<?php

namespace App\Filament\Actions;

use App\Models\User;
use App\Status;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class ChangeReservation
{
    public static function make(): Action
    {
        return Action::make('reserve_date')
            ->icon('heroicon-s-calendar')
            ->visible(function ($record) {
//                $user = auth()->user();
//                $isVisible = $record->employee_id && $user?->roles->contains('slug', 'employee');
                $isVisible = $record->status === Status::BOOKED;
                return $isVisible;
            })
            ->schema([
                DateTimePicker::make('reserved_date')
                    ->label(__('coupon.form.reserved_date'))
                    ->required(),
                Select::make('sungard_branch_id')
                    ->label(__('coupon.form.sungard_branch_name'))
                    ->relationship('sungard', 'name')
                    ->required()

            ])
            ->action(function (array $data, Model $record): void {
                $record->update([
                    'reserved_date' => $data['reserved_date'],
                    'sungard_branch_id' => $data['sungard_branch_id'],
                ]);

                $usersToNotify = User::where('sungard_branch_id', $data['sungard_branch_id'])
                    ->get();
                foreach ($usersToNotify as $user) {
                    Notification::make()
                        ->title(__('coupon.notification.new_reservation.title'))
                        ->body(__('coupon.notification.new_reservation.body', ['employee_name' => $record->employee?->name]))
                        ->success()
                        ->sendToDatabase($user);
                }

            })
            ->successNotificationTitle(__('coupon.notification.reservation_success.title'));
    }
}
