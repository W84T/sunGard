<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Overtrue\LaravelVersionable\Versionable;
use Overtrue\LaravelVersionable\VersionStrategy;
use Spatie\Permission\Traits\HasRoles;

class Branch extends Model
{
    use SoftDeletes;
    use HasRoles;
    use Versionable;

    protected $guarded = [];

    protected $versionable = ['name', 'address'];

    protected $versionStrategy = VersionStrategy::SNAPSHOT;

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exhibition(): BelongsTo
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function coupon(): HasMany
    {
        return $this->hasMany(Coupon::class);
    }
}
