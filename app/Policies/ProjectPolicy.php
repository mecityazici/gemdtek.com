<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'editor', 'team_captain']);
    }

    public function view(User $user, Project $project): bool
    {
        return $this->isAdminOrEditor($user)
            || $this->isCaptainOfProject($user, $project);
    }

    public function create(User $user): bool
    {
        return $this->isAdminOrEditor($user);
    }

    public function update(User $user, Project $project): bool
    {
        return $this->isAdminOrEditor($user)
            || $this->isCaptainOfProject($user, $project);
    }

    public function delete(User $user, Project $project): bool
    {
        return $this->isAdminOrEditor($user);
    }

    public function restore(User $user, Project $project): bool
    {
        return $this->isAdminOrEditor($user);
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return $user->hasRole('super_admin');
    }

    public function reorder(User $user): bool
    {
        return $this->isAdminOrEditor($user);
    }

    private function isAdminOrEditor(User $user): bool
    {
        return $user->hasAnyRole(['super_admin', 'editor']);
    }

    private function isCaptainOfProject(User $user, Project $project): bool
    {
        return $user->hasRole('team_captain')
            && $project->captain_user_id === $user->id;
    }
}
