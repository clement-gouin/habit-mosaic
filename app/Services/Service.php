<?php

namespace App\Services;

use Illuminate\Support\Facades\App;

abstract class Service
{
    public static function instance(): static
    {
        return App::make(static::class);
    }
}
