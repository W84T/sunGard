<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class SungardBranches extends Model
{
    use HasFactory;
    use HasRoles;

    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class, 'sungard_branch_id');
    }
}
