<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Permission\Traits\HasRoles;

use App\Enums\TicketPriority;

class Ticket extends Model
{
    use HasFactory;
    use HasRoles;

    protected $guarded = [];

    protected $casts = [
        'priority' => TicketPriority::class,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class, 'coupon_id');
    }
}
