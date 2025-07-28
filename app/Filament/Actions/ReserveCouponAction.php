<?php

namespace App\Filament\Actions;

use App\Models\Coupon;
use App\Models\CouponReservation;
use App\Models\Settings;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ReserveCouponAction
{
    public static function make(): Action
    {
        return Action::make('reserve')
            ->label('Reserve Coupon')
            ->icon('heroicon-o-bookmark')
            ->requiresConfirmation()
            ->schema([])
            ->visible(function ($record) {
                $user = auth()->user();
                $isVisible = !$record->employee_id && $user?->roles->contains('slug', 'employee');

                return $isVisible;
            })
            ->action(function ($record) {
                $user = Auth::user();

                if (!$user) {
                    Notification::make()
                        ->title('Authentication Error')
                        ->body('You must be logged in to reserve a coupon.')
                        ->danger()
                        ->send();
                    return;
                }

                if (!$record instanceof Coupon) {
                    Notification::make()
                        ->title('Invalid Coupon')
                        ->body('This record is not a valid coupon.')
                        ->danger()
                        ->send();
                    return;
                }

                $max = Settings::get('max_active_coupons', 5);

                $cooldownMinutes = (int) Settings::get('reservation_cooldown_minutes', 10);

                $lastReservation = CouponReservation::where('employee_id', $user->id)
                    ->latest('reserved_at')
                    ->first();

                if ($lastReservation && $lastReservation->reserved_at >= now()->subMinutes($cooldownMinutes)) {
                    $nextAvailable = $lastReservation->reserved_at->addMinutes($cooldownMinutes);
                    $waitMinutes = ceil(now()->diffInRealMinutes($nextAvailable));

                    Notification::make()
                        ->title('Cooldown Active')
                        ->body("Please wait {$waitMinutes} more minute(s) before reserving another coupon.")
                        ->warning()
                        ->send();

                    return;
                }


                // Old logic: Check active (non-completed) coupons from Coupon table
                // $activeCount = Coupon::where('employee_id', $user->id)
                //     ->whereNotIn('status', ['completed', 'cancelled']) // or use your enum: Status::getBookedCases()
                //     ->count();

                // New logic: Count how many reservations this user made today
                $activeCount = CouponReservation::where('employee_id', $user->id)
                    ->whereDate('reserved_at', now()->toDateString())
                    ->count();

                if ($activeCount >= $max) {
                    Notification::make()
                        ->title('Limit Reached')
                        ->body('You’ve reached your daily coupon reservation limit (' . $max . ').')
                        ->warning()
                        ->send();
                    return;
                }

                $record->update([
                    'employee_id' => $user->id,
                    'status' => '0',
                ]);

                CouponReservation::create([
                    'coupon_id' => $record->id,
                    'employee_id' => $user->id,
                    'reserved_at' => now(),
                ]);

                Notification::make()
                    ->title('Success')
                    ->body('Coupon successfully reserved!')
                    ->success()
                    ->send();
            });
    }
}
