<?php

namespace App\Events;

use App\Models\Category;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryWiped
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Category $category)
    {
    }
}
