<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Status;
use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Coupon;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Coupon');
    }

    public function view(AuthUser $authUser, Coupon $coupon): bool
    {
        if($authUser->isCustomerService() && !$coupon->status ){
            return false;
        }

        return $authUser->can('View:Coupon');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Coupon');
    }

    public function update(AuthUser $authUser, Coupon $coupon): bool
    {
        return $authUser->can('Update:Coupon');
    }

    public function delete(AuthUser $authUser, Coupon $coupon): bool
    {
        return $authUser->can('Delete:Coupon');
    }

    public function restore(AuthUser $authUser, Coupon $coupon): bool
    {
        return $authUser->can('Restore:Coupon');
    }

    public function forceDelete(AuthUser $authUser, Coupon $coupon): bool
    {
        return $authUser->can('ForceDelete:Coupon');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Coupon');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Coupon');
    }

    public function replicate(AuthUser $authUser, Coupon $coupon): bool
    {
        return $authUser->can('Replicate:Coupon');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Coupon');
    }

    public function submitTicket(AuthUser $authUser, Coupon $coupon): bool
    {
        return $coupon->status && $authUser->can('SubmitTicket:Coupon');
    }

    public function reserve(AuthUser $authUser, Coupon $coupon): bool
    {
        return !$coupon->employee_id &&$authUser->can('ReserveCoupon:Coupon');
    }

    public function changeStatus(AuthUser $authUser, Coupon $coupon): bool
    {
        // Keep the permission check
        if (!$authUser->can('ChangeStatus:Coupon')) {
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

    public function revision(AuthUser $authUser, Coupon $coupon): bool
    {
        return $authUser->can('Revision:Coupon');
    }
}
