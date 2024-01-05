<?php

namespace App\Events;

use App\Models\User;
use App\Models\Tracker;
use App\Interfaces\WithUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrackerUpdated implements WithUser
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Tracker $tracker)
    {
    }

    public function getUser(): User
    {
        return $this->tracker->user;
    }
}
