<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Tracker;

class TrackerPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Tracker $tracker): bool
    {
        return $tracker->user->id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Tracker $tracker): bool
    {
        return $tracker->user->id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Tracker $tracker): bool
    {
        return $tracker->user->id === $user->id;
    }
}
