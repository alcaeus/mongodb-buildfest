<?php

namespace App\MongoDB;

interface TypeMapAware
{
    public static function getTypeMap(): array;
}
