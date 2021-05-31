<?php

namespace App\Model\Level;

use App\DataType\FirstLevelFlag;
use App\DataType\LevelAdvertisingText;
use App\DataType\LevelDescription;
use App\DataType\LevelId;
use App\DataType\LevelName;
use Tools\models\ModelInterface;

interface LevelInterface extends ModelInterface
{
    /**
     * @return LevelId
     */
    public function getId(): LevelId;

    /**
     * @return LevelName
     */
    public function getName(): LevelName;

    /**
     * @return LevelDescription
     */
    public function getDescription(): LevelDescription;

    /**
     * @return LevelAdvertisingText
     */
    public function getAdvertisingText(): LevelAdvertisingText;

    /**
     * @return LevelId
     */
    public function getParentLevel(): LevelId;

    /**
     * @return FirstLevelFlag
     */
    public function getIsFirst(): FirstLevelFlag;
}
