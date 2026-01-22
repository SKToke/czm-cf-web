<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class StringHelper extends Str
{
    public static function toTitle(string $str): string
    {
        return ucwords(str_replace(
            '_',
            ' ',
            str_replace('-', ' ', mb_strtolower($str))
        ));
    }
}
