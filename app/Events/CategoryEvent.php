<?php

namespace App\Events;

use App\Interfaces\WithUser;
use App\Models\Category;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class CategoryEvent implements WithUser
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
