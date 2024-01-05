<?php

namespace App\Events;

use App\Models\User;
use App\Models\Category;
use App\Interfaces\WithUser;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryUpdated implements WithUser
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Category $category)
    {
    }

    public function getUser(): User
    {
        return $this->category->user;
    }
}
