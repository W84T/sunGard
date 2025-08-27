<?php

use App\Models\Coupon;
use App\Services\CouponImageService;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//    return view('welcome');
// });



Route::get('/coupons/{coupon}/preview', function (Coupon $coupon, CouponImageService $svc) {
    // Regenerate now (blocking)
    $relativePath = $svc->render($coupon);
    if ($relativePath === '') {
        abort(404, 'Unable to render coupon image');
    }

    // Persist quietly to avoid triggering observers
    $coupon->updateQuietly(['coupon_link' => $relativePath]);

    // Serve the file from the public disk
    $absolutePath = Storage::disk('public')->path($relativePath);
    if (! file_exists($absolutePath)) {
        abort(404, 'Image not found');
    }

    return response()->file($absolutePath, [
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
    ]);
})->name('coupon.preview');
