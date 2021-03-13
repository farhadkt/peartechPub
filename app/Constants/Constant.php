<?php


namespace App\Constants;


abstract class Constant
{
    public static function list ()
    {
        $reflection = new \ReflectionClass(static::class);

        return $reflection->getConstants();
    }

    public static function listReverse ()
    {
        $reflection = new \ReflectionClass(static::class);

        return array_flip($reflection->getConstants());
    }

    public static function keys ()
    {
        $reflection = new \ReflectionClass(static::class);

        return array_keys($reflection->getConstants());
    }

    public static function values ()
    {
        $reflection = new \ReflectionClass(static::class);

        return array_values($reflection->getConstants());
    }
}
