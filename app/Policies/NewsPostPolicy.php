<?php

namespace App\Policies;

use App\Models\NewsPost;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NewsPostPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_news::post');
    }

    public function view(User $user, NewsPost $newsPost): bool
    {
        return $user->can('view_news::post');
    }

    public function create(User $user): bool
    {
        return $user->can('create_news::post');
    }

    public function update(User $user, NewsPost $newsPost): bool
    {
        return $user->can('update_news::post');
    }

    public function delete(User $user, NewsPost $newsPost): bool
    {
        return $user->can('delete_news::post');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_news::post');
    }

    public function forceDelete(User $user, NewsPost $newsPost): bool
    {
        return $user->can('force_delete_news::post');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_news::post');
    }

    public function restore(User $user, NewsPost $newsPost): bool
    {
        return $user->can('restore_news::post');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_news::post');
    }

    public function replicate(User $user, NewsPost $newsPost): bool
    {
        return $user->can('replicate_news::post');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_news::post');
    }
}
