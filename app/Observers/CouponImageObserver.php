<?php

// app/Observers/CouponImageObserver.php
namespace App\Observers;

use App\Jobs\RenderCouponImageJob;
use App\Models\Coupon;
use Illuminate\Support\Facades\Storage;

class CouponImageObserver
{
    public function created(Coupon $coupon): void
    {
        RenderCouponImageJob::dispatch($coupon->id)->afterCommit();

    }

    public function updated(Coupon $coupon): void
    {
        if ($this->shouldRegenerate($coupon)) {
            RenderCouponImageJob::dispatch($coupon->id)->afterCommit();
        }
    }

    // If you want to delete the image only on hard delete, use forceDeleted:
    public function forceDeleted(Coupon $coupon): void
    {
        if ($coupon->coupon_link) {
            Storage::disk('public')->delete($coupon->coupon_link);
        }
    }

    // If you also want to delete on soft delete, add:
    // public function deleted(Coupon $coupon): void { ... }

    protected function shouldRegenerate(Coupon $coupon): bool
    {
        // Regenerate only when fields affecting the artwork change
        $keys = [
            'customer_name','customer_phone','customer_email',
            'car_model','car_brand','plate_number','plate_characters','car_category',
            'plans','exhibition_id','agent_id','created_at',
        ];
        foreach ($keys as $k) {
            if ($coupon->wasChanged($k)) return true;
        }
        // Optional: regenerate when status crosses important milestones and you print it
        // if ($coupon->wasChanged('status')) return true;

        return false;
    }
}
