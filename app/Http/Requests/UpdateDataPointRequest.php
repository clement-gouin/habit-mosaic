<?php

namespace App\Http\Requests;

use App\Models\User;

class UpdateDataPointRequest extends StoreTrackerRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var User $user */
        $user = $this->user();
        return $user->can('update', $this->route('data_point'));
    }
}
