<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_coupons::coupon');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Coupon $coupon)
    {
        return ($user->can('view_coupons::coupon') && ! $user->roles->contains('slug', 'employee')) || $coupon->employee_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_coupons::coupon');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Coupon $coupon): bool
    {
        return ($user->can('update_coupons::coupon') && ! $user->roles->contains('slug', 'employee')) || $coupon->employee_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Coupon $coupon): bool
    {
        return $user->can('delete_coupons::coupon');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_coupons::coupon');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Coupon $coupon): bool
    {
        return $user->can('force_delete_coupons::coupon');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_coupons::coupon');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Coupon $coupon): bool
    {
        return $user->can('restore_coupons::coupon');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_coupons::coupon');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Coupon $coupon): bool
    {
        return $user->can('replicate_coupons::coupon');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_coupons::coupon');
    }
}
