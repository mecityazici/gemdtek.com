<?php

namespace App\Policies;

use App\Models\SiteMetric;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SiteMetricPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_site::metric');
    }

    public function view(User $user, SiteMetric $siteMetric): bool
    {
        return $user->can('view_site::metric');
    }

    public function create(User $user): bool
    {
        return $user->can('create_site::metric');
    }

    public function update(User $user, SiteMetric $siteMetric): bool
    {
        return $user->can('update_site::metric');
    }

    public function delete(User $user, SiteMetric $siteMetric): bool
    {
        return $user->can('delete_site::metric');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_site::metric');
    }

    public function forceDelete(User $user, SiteMetric $siteMetric): bool
    {
        return $user->can('force_delete_site::metric');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_site::metric');
    }

    public function restore(User $user, SiteMetric $siteMetric): bool
    {
        return $user->can('restore_site::metric');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_site::metric');
    }

    public function replicate(User $user, SiteMetric $siteMetric): bool
    {
        return $user->can('replicate_site::metric');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_site::metric');
    }
}
