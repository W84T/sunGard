<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'created_by',
        'email_verified_at',
        'branch_id',
        'exhibition_id',
        'sungard_branch_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function exhibition(): BelongsTo
    {
        return $this->belongsTo(Exhibition::class);
    }

    public function subgard(): BelongsTo
    {
        return $this->belongsTo(SungardBranches::class, 'sungard_branch_id');

    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function couponsHandled(): HasMany
    {
        return $this->hasMany(Coupon::class, 'employee_id');
    }

    public function couponsCreated(): HasMany
    {
        return $this->hasMany(Coupon::class, 'agent_id');
    }

    public function hasAnyRoleSlug(array $slugs): bool
    {
        return $this->roles->pluck('slug')
            ->intersect($slugs)
            ->isNotEmpty();
    }

    public function isCustomerService(): bool
    {
        return $this->hasRoleSlug('customer service');
    }

    public function hasRoleSlug(string $slug): bool
    {
        return $this->roles->contains('slug', $slug);
    }

    public function createdUsers(): HasMany
    {
        return $this->hasMany(User::class, 'created_by');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles()->exists();
    }
}
