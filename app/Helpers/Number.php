<?php


namespace App\Helpers;


class Number
{
    public static function trimTrailingZeroes($nbr)
    {
        return strpos($nbr, '.') !== false ? rtrim(rtrim($nbr, '0'), '.') : $nbr;
    }
}
