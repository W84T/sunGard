<?php

// app/Jobs/RenderCouponImageJob.php
namespace App\Jobs;

use App\Models\Coupon;
use App\Services\CouponImageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RenderCouponImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public function __construct(public int $couponId) {
        $this->afterCommit = true;
    }

    public function handle(CouponImageService $svc): void
    {
        $coupon = Coupon::with(['exhibitionRelation','agent'])->find($this->couponId);
        if (! $coupon) return;

        $path = $svc->render($coupon);
        if ($path !== '') {
            $coupon->updateQuietly(['coupon_link' => $path]);
        }
    }
}
