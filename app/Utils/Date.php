<?php

namespace App\Utils;

use Illuminate\Support\Carbon;
use Carbon\Exceptions\InvalidFormatException;

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
