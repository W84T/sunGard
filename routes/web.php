<?php

use App\Http\Controllers\CouponPdfController;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//    return view('welcome');
// });


Route::get('/coupon/preview', [CouponPdfController::class, 'preview'])->name('coupon.preview');
