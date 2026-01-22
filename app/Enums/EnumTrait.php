<?php

namespace App\Enums;

use App\Helpers\StringHelper;

trait EnumTrait
{
    public static function toArray()
    {
        $options = [];

        foreach (static::cases() as $case) {
            $options[$case->value] = $case->getTitle();
        }

        return $options;
    }

    public function getTitle(): string
    {
        return StringHelper::toTitle($this->name);
    }
}
