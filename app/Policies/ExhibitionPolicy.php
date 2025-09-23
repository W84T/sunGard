<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Exhibition;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExhibitionPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Exhibition');
    }

    public function view(AuthUser $authUser, Exhibition $exhibition): bool
    {
        return $authUser->can('View:Exhibition');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Exhibition');
    }

    public function update(AuthUser $authUser, Exhibition $exhibition): bool
    {
        return $authUser->can('Update:Exhibition');
    }

    public function delete(AuthUser $authUser, Exhibition $exhibition): bool
    {
        return $authUser->can('Delete:Exhibition');
    }

    public function restore(AuthUser $authUser, Exhibition $exhibition): bool
    {
        return $authUser->can('Restore:Exhibition');
    }

    public function forceDelete(AuthUser $authUser, Exhibition $exhibition): bool
    {
        return $authUser->can('ForceDelete:Exhibition');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Exhibition');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Exhibition');
    }

    public function replicate(AuthUser $authUser, Exhibition $exhibition): bool
    {
        return $authUser->can('Replicate:Exhibition');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Exhibition');
    }

    public function addPlansAndDiscounts(AuthUser $authUser, Exhibition $exhibition): bool
    {
        return $authUser->can('AddPlansAndDiscounts:Exhibition');
    }
}
