<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Exhibition extends Model
{
    use HasFactory, SoftDeletes;

    //    use Versionable;
    use HasRoles;

    protected $guarded = [];

    public function creator(): belongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branches()
    {
        return $this->hasMany(Branch::class, 'exhibition_id');
    }
}
