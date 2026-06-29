<?php

namespace App\Policies;

use App\Models\SponsorLead;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SponsorLeadPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_sponsor::lead');
    }

    public function view(User $user, SponsorLead $sponsorLead): bool
    {
        return $user->can('view_sponsor::lead');
    }

    public function create(User $user): bool
    {
        return $user->can('create_sponsor::lead');
    }

    public function update(User $user, SponsorLead $sponsorLead): bool
    {
        return $user->can('update_sponsor::lead');
    }

    public function delete(User $user, SponsorLead $sponsorLead): bool
    {
        return $user->can('delete_sponsor::lead');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_sponsor::lead');
    }

    public function forceDelete(User $user, SponsorLead $sponsorLead): bool
    {
        return $user->can('force_delete_sponsor::lead');
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_sponsor::lead');
    }

    public function restore(User $user, SponsorLead $sponsorLead): bool
    {
        return $user->can('restore_sponsor::lead');
    }

    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_sponsor::lead');
    }

    public function replicate(User $user, SponsorLead $sponsorLead): bool
    {
        return $user->can('replicate_sponsor::lead');
    }

    public function reorder(User $user): bool
    {
        return $user->can('reorder_sponsor::lead');
    }
}
