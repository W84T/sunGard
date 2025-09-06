<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Permission\Traits\HasRoles;

class SungardBranches extends Model
{
    use HasRoles;

    protected $guarded = [];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'sungard_branch_id');
    }

    public function coupon(): hasMany
    {
        return $this->hasmany(Coupon::class, 'sungard_branch_id');
    }
}
