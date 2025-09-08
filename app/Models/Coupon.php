<?php

namespace App\Models;

use App\Status;
use ArPHP\I18N\Arabic;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Overtrue\LaravelVersionable\Versionable;
use Overtrue\LaravelVersionable\VersionStrategy;
use Spatie\Permission\Traits\HasRoles;

class Coupon extends Model
{
    use SoftDeletes, HasRoles, Versionable;

    protected $guarded = [];

    protected $versionable = [
        'agent_id',
        'branch_id',
        'exhibition_id',
        'employee_id',
        'sungard_branch_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'car_model',
        'car_brand',
        'plate_number',
        'plate_characters',
        'car_category',
        'is_confirmed',
        'reserved_date',
    ];

    protected $versionStrategy = VersionStrategy::SNAPSHOT;

    protected $casts = [
        'status' => Status::class,
        'reserved_date' => 'datetime',
        'reached_at' => 'datetime',
        'is_confirmed' => 'bool',
        'plans' => 'array', // ✅ cast JSON automatically
    ];

    /*
    |--------------------------------------------------------------------------
    | Booted Events
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::updating(function ($coupon) {
            $originalStatus = $coupon->getOriginal('status');
            $newStatus = $coupon->status instanceof Status
                ? $coupon->status
                : Status::tryFrom((int)$coupon->status);

            $originalStatusEnum = $originalStatus instanceof Status
                ? $originalStatus
                : Status::tryFrom((int)$originalStatus);

            if ($originalStatusEnum?->isReserved() && $newStatus?->value !== $originalStatusEnum?->value) {
                $coupon->reached_at = now();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function branchRelation(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function exhibitionRelation(): BelongsTo
    {
        return $this->belongsTo(Exhibition::class, 'exhibition_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function sungard(): BelongsTo
    {
        return $this->belongsTo(SungardBranches::class, 'sungard_branch_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'coupon_id');
    }

    public function openTicket(): HasOne
    {
        return $this->hasOne(Ticket::class, 'coupon_id')
            ->where('status', 'open')
            ->latest();
    }


    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */
    public function getCarPlateAttribute(): string
    {
        return strtoupper($this->plate_characters) . '-' . $this->plate_number;
    }
}
