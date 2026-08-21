<?php

namespace App\Policies;

use App\Models\Organizer;
use App\Models\User;

class OrganizerPolicy
{
    /**
     * Determine whether the user can view any models.
     *
     * The organizer directory is global and readable by every authenticated user,
     * because games are recorded by picking an organizer from it.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organizer $organizer): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organizer $organizer): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organizer $organizer): bool
    {
        return $user->isAdmin();
    }
}
