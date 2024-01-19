<?php

namespace App\Events;

use App\Interfaces\WithUser;
use App\Models\Tracker;
use App\Models\User;
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
