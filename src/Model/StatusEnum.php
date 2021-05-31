<?php

namespace App\Model;

use Tools\types\base\EnumType;

class StatusEnum extends EnumType
{
    public const ACTIVE = 1;
    public const DISABLED = 2;
}
