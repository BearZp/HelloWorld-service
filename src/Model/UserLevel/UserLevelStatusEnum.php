<?php

namespace App\Model\UserLevel;

use Tools\types\base\EnumType;

class UserLevelStatusEnum extends EnumType
{
    public const VERIFIED = 1;
    public const NEED_VERIFICATION = 2;
}
