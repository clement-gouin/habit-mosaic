<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DataPoint;

class DataPointPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DataPoint $dataPoint): bool
    {
        return $dataPoint->tracker->user->id === $user->id;
    }
}
