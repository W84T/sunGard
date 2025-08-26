<?php

use App\Models\Coupon;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//    return view('welcome');
// });



Route::get('/coupon/{id}/preview', function ($id) {
    $coupon = Coupon::findOrFail($id);

    // Regenerate image every time this URL is hit
    $coupon->generateCouponImage();

    $path = public_path($coupon->coupon_link);

    return response()->file($path);
});
