<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Earning extends Model
{
    use HasRoles;
    use HasFactory, SoftDeletes;

    protected $guarded = [];
}
