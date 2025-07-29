<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Overtrue\LaravelVersionable\Versionable;
use Spatie\Permission\Traits\HasRoles;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;
    use HasRoles;
    use Versionable;

    protected $guarded = [];

    protected $casts = [
        'status' => \App\Status::class,
    ];


//    protected static function booted(): void
//    {
//        static::updating(function ($coupon) {
//            $originalStatus = $coupon->getOriginal('status');
//            $newStatus = $coupon->status;
//
//            // Only act if status is actually changing
//            if ($originalStatus !== $newStatus) {
//                $user = Auth::user();
//
//                if ($user && $user->roles->contains('slug', 'employee')) {
//                    $coupon->employee_id= $user->id;
//                }
//            }
//        });
//    }

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
}
