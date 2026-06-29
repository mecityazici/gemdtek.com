<?php

namespace App\Policies;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamMemberPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_team::member');
    }

    public function view(User $user, TeamMember $teamMember): bool
    {
        return $user->can('view_team::member');
    }

    public function create(User $user): bool
    {
        return $user->can('create_team::member');
    }

    public function update(User $user, TeamMember $teamMember): bool
    {
        return $user->can('update_team::member');
    }

    public function delete(User $user, TeamMember $teamMember): bool
    {
        return $user->can('delete_team::member');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_team::member');
    }

    public function forceDelete(User $user, TeamMember $teamMember): bool
    {
        return $user->can('force_delete_team::member');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_team::member');
    }

    public function restore(User $user, TeamMember $teamMember): bool
    {
        return $user->can('restore_team::member');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_team::member');
    }

    public function replicate(User $user, TeamMember $teamMember): bool
    {
        return $user->can('replicate_team::member');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_team::member');
    }
}
