<?php

namespace App\Policies;

use App\Models\Alumni;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlumniPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_alumni');
    }

    public function view(User $user, Alumni $alumni): bool
    {
        return $user->can('view_alumni');
    }

    public function create(User $user): bool
    {
        return $user->can('create_alumni');
    }

    public function update(User $user, Alumni $alumni): bool
    {
        return $user->can('update_alumni');
    }

    public function delete(User $user, Alumni $alumni): bool
    {
        return $user->can('delete_alumni');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_alumni');
    }

    public function forceDelete(User $user, Alumni $alumni): bool
    {
        return $user->can('force_delete_alumni');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_alumni');
    }

    public function restore(User $user, Alumni $alumni): bool
    {
        return $user->can('restore_alumni');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_alumni');
    }

    public function replicate(User $user, Alumni $alumni): bool
    {
        return $user->can('replicate_alumni');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_alumni');
    }
}
