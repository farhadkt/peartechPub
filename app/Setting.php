<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    public static function key($name, $decode = 0)
    {
        if($result = self::where('name', $name)->first()) {
            if ($decode === 1) $result->value = json_decode($result->value);
        }

        return $result->value;
    }
}
