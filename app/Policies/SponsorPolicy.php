<?php

namespace App\Policies;

use App\Models\Sponsor;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SponsorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_sponsor');
    }

    public function view(User $user, Sponsor $sponsor): bool
    {
        return $user->can('view_sponsor');
    }

    public function create(User $user): bool
    {
        return $user->can('create_sponsor');
    }

    public function update(User $user, Sponsor $sponsor): bool
    {
        return $user->can('update_sponsor');
    }

    public function delete(User $user, Sponsor $sponsor): bool
    {
        return $user->can('delete_sponsor');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_sponsor');
    }

    public function forceDelete(User $user, Sponsor $sponsor): bool
    {
        return $user->can('force_delete_sponsor');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_sponsor');
    }

    public function restore(User $user, Sponsor $sponsor): bool
    {
        return $user->can('restore_sponsor');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_sponsor');
    }

    public function replicate(User $user, Sponsor $sponsor): bool
    {
        return $user->can('replicate_sponsor');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_sponsor');
    }
}
