<?php

namespace App\Events;

use App\Models\DataPoint;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class DataPointEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public DataPoint $dataPoint)
    {
    }
}
