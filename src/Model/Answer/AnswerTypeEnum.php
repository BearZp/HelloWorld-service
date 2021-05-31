<?php

namespace App\Model\Answer;

use Tools\types\base\EnumType;

class AnswerTypeEnum extends EnumType
{
    public const TEXT = 1;
    public const SELECT = 2;
    public const DATE = 3;
    public const FILE = 4;
}
