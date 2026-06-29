<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_event');
    }

    public function view(User $user, Event $event): bool
    {
        return $user->can('view_event');
    }

    public function create(User $user): bool
    {
        return $user->can('create_event');
    }

    public function update(User $user, Event $event): bool
    {
        return $user->can('update_event');
    }

    public function delete(User $user, Event $event): bool
    {
        return $user->can('delete_event');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_event');
    }

    public function forceDelete(User $user, Event $event): bool
    {
        return $user->can('force_delete_event');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_event');
    }

    public function restore(User $user, Event $event): bool
    {
        return $user->can('restore_event');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_event');
    }

    public function replicate(User $user, Event $event): bool
    {
        return $user->can('replicate_event');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_event');
    }
}
