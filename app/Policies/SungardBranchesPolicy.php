<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SungardBranches;
use Illuminate\Auth\Access\HandlesAuthorization;

class SungardBranchesPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SungardBranches');
    }

    public function view(AuthUser $authUser, SungardBranches $sungardBranches): bool
    {
        return $authUser->can('View:SungardBranches');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SungardBranches');
    }

    public function update(AuthUser $authUser, SungardBranches $sungardBranches): bool
    {
        return $authUser->can('Update:SungardBranches');
    }

    public function delete(AuthUser $authUser, SungardBranches $sungardBranches): bool
    {
        return $authUser->can('Delete:SungardBranches');
    }

    public function restore(AuthUser $authUser, SungardBranches $sungardBranches): bool
    {
        return $authUser->can('Restore:SungardBranches');
    }

    public function forceDelete(AuthUser $authUser, SungardBranches $sungardBranches): bool
    {
        return $authUser->can('ForceDelete:SungardBranches');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SungardBranches');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SungardBranches');
    }

    public function replicate(AuthUser $authUser, SungardBranches $sungardBranches): bool
    {
        return $authUser->can('Replicate:SungardBranches');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SungardBranches');
    }

}