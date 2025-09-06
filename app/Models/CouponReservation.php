<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CouponReservation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'reserved_at' => 'datetime',
    ];
}
