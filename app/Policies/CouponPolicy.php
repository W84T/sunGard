<?php

namespace App\Policies;

use App\Models\Coupon;
use App\Models\User;
use App\Status;
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
    public function view(User $user, Coupon $coupon): bool
    {
        return $user->can('view_coupons::coupon');
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
        return $user->can('update_coupons::coupon');
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
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('{{ Reorder }}');
    }

    public function submitTicket(User $user, Coupon $coupon): bool
    {
        return $coupon->status && $user->can('submit_ticket_coupons::coupon');
    }

    public function reserve(User $user, Coupon $coupon): bool
    {
        return !$coupon->employee_id &&$user->can('reserve_coupon_coupons::coupon');
    }

    public function changeStatus(User $user, Coupon $coupon): bool
    {
        // Keep the permission check
        if (!$user->can('change_status_coupons::coupon')) {
            return false;
        }

        $status = $coupon->status;

        // 1. No status → no action
        if ($status === null) {
            return false;
        }

        // 2. Normalize status into enum
        $status = $status instanceof Status
            ? $status
            : Status::tryFrom((int)$status);

        // 3. Invalid enum → no action
        if (!$status) {
            return false;
        }

        // 4. Reserved → always allow
        if ($status->isReserved()) {
            return true;
        }

        // 5. Scheduled → only allow if confirmed
        if ($status->isScheduled()) {
            return (bool)$coupon->is_confirmed;
        }

        // 6. Everything else → allow
        return true;
    }



}
