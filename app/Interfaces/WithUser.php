<?php

namespace App\Interfaces;

use App\Models\User;

interface WithUser
{
    public function getUser(): User;
}
