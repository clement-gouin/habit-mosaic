<?php

namespace App\Utils;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Carbon;

abstract class Date
{
    public static function parse(string $rawDate): Carbon
    {
        try {
            return Carbon::parse($rawDate);
        } catch (InvalidFormatException) {
            return Carbon::today();
        }
    }
}
