<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /*
     * Kullanıcı + rol yönetimi YALNIZCA super_admin'e ait. Bu policy olmadan
     * Filament policy-yokluğunda fail-open yapıyor ve herhangi bir panel rolü
     * (editor / team_captain) UserResource üzerinden kendine super_admin
     * atayabiliyordu (privilege escalation). super_admin Shield Gate::before ile
     * zaten bypass eder; burada açıkça super_admin'e kilitliyoruz.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function view(User $user, User $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super_admin');
    }

    public function update(User $user, User $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function delete(User $user, User $model): bool
    {
        return $user->hasRole('super_admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('super_admin');
    }
}
