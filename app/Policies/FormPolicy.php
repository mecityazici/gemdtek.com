<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_form');
    }

    public function view(User $user, Form $form): bool
    {
        return $user->can('view_form');
    }

    public function create(User $user): bool
    {
        return $user->can('create_form');
    }

    public function update(User $user, Form $form): bool
    {
        return $user->can('update_form');
    }

    public function delete(User $user, Form $form): bool
    {
        return $user->can('delete_form');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_form');
    }

    public function forceDelete(User $user, Form $form): bool
    {
        return $user->can('force_delete_form');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_form');
    }

    public function restore(User $user, Form $form): bool
    {
        return $user->can('restore_form');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_form');
    }

    public function replicate(User $user, Form $form): bool
    {
        return $user->can('replicate_form');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_form');
    }
}
