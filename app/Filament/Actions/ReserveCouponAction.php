<?php

namespace App\Filament\Actions;

use App\Models\Coupon;
use App\Models\CouponReservation;
use App\Models\Settings;
use App\Status;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ReserveCouponAction
{
    public static function make(): Action
    {
        return Action::make('reserve')
            ->label(__('Reserve Coupon'))
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
                        ->title('خطأ في المصادقة')
                        ->body('يجب عليك تسجيل الدخول لحجز قسيمة.')
                        ->danger()
                        ->send();
                    return;
                }

                if (!$record instanceof Coupon) {
                    Notification::make()
                        ->title('قسيمة غير صالحة')
                        ->body('هذا السجل ليس قسيمة صالحة.')
                        ->danger()
                        ->send();
                    return;
                }

                $max = Settings::get('max_active_coupons', 5);

                $cooldownMinutes = (int)Settings::get('reservation_cooldown_minutes', 10);

                $lastReservation = CouponReservation::where('employee_id', $user->id)
                    ->latest('reserved_at')
                    ->first();

                // get the coupon that related to the customer but it's status is reserved

                $coupon = Coupon::where('employee_id', $user->id)
                    ->where('status', Status::RESERVED)
                    ->count();

//                if ($lastReservation && $lastReservation->reserved_at >= now()->subMinutes($cooldownMinutes))
                if (!$coupon == 0) {
//                    $nextAvailable = $lastReservation->reserved_at->addMinutes($cooldownMinutes);
//                    $waitMinutes = ceil(now()->diffInRealMinutes($nextAvailable));

                    Notification::make()
                        ->title('فترة تهدئة نشطة')
                        ->body("الرجاء معالجة الكوبون المحجوز قبل حجز كوبون جديد")
                        ->warning()
                        ->send();

                    return;
                }


                // Old logic: Check active (non-completed) coupons from Coupon table
                // $activeCount = Coupon::where('employee_id', $user->id)
                //     ->whereNotIn('status', ['completed', 'cancelled']) // or use your enum: Status::getBookedCases()
                //     ->count();

                // New logic: Count how many reservations this user made today
//                $activeCount = CouponReservation::where('employee_id', $user->id)
//                    ->whereDate('reserved_at', now()->toDateString())
//                    ->count();
//
//                if ($activeCount >= $max) {
//                    Notification::make()
//                        ->title('تم الوصول إلى الحد الأقصى')
//                        ->body('لقد وصلت إلى الحد الأقصى اليومي لحجز القسائم (' . $max . ').')
//                        ->warning()
//                        ->send();
//                    return;
//                }

                $record->update([
                    'employee_id' => $user->id,
                    'status' => Status::RESERVED,
                ]);

                CouponReservation::create([
                    'coupon_id' => $record->id,
                    'employee_id' => $user->id,
                    'reserved_at' => now(),
                ]);

                Notification::make()
                    ->title('نجاح')
                    ->body('تم حجز القسيمة بنجاح!')
                    ->success()
                    ->send();
            });
    }
}
