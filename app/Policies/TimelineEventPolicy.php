<?php

namespace App\Policies;

use App\Models\TimelineEvent;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TimelineEventPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_timeline::event');
    }

    public function view(User $user, TimelineEvent $timelineEvent): bool
    {
        return $user->can('view_timeline::event');
    }

    public function create(User $user): bool
    {
        return $user->can('create_timeline::event');
    }

    public function update(User $user, TimelineEvent $timelineEvent): bool
    {
        return $user->can('update_timeline::event');
    }

    public function delete(User $user, TimelineEvent $timelineEvent): bool
    {
        return $user->can('delete_timeline::event');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_timeline::event');
    }

    public function forceDelete(User $user, TimelineEvent $timelineEvent): bool
    {
        return $user->can('force_delete_timeline::event');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_timeline::event');
    }

    public function restore(User $user, TimelineEvent $timelineEvent): bool
    {
        return $user->can('restore_timeline::event');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_timeline::event');
    }

    public function replicate(User $user, TimelineEvent $timelineEvent): bool
    {
        return $user->can('replicate_timeline::event');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_timeline::event');
    }
}
