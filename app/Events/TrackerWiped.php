<?php

namespace App\Events;

use App\Models\Tracker;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrackerWiped
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Tracker $tracker)
    {
    }
}
