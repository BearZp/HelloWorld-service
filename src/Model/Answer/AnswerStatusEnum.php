<?php

namespace App\Model\Answer;

use Tools\types\base\EnumType;

class AnswerStatusEnum extends EnumType
{
    public const VERIFIED = 1;
    public const NEED_VERIFICATION = 2;
}
