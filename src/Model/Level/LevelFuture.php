<?php

namespace App\Model\Level;

use App\DataType\FirstLevelFlag;
use App\DataType\LevelAdvertisingText;
use App\DataType\LevelDescription;
use App\DataType\LevelId;
use App\DataType\LevelName;
use Tools\models\mappers\MapperInterface;
use Tools\models\FutureObjectInterface;
use Tools\models\FutureObjectTrait;
use Throwable;

class LevelFuture implements LevelInterface, FutureObjectInterface
{
    use FutureObjectTrait {
        FutureObjectTrait::get as getModel;
    }

    /**
     * @return LevelId
     * @throws Throwable
     */
    public function getId(): LevelId
    {
        return $this->getModel()->getId();
    }

    /**
     * @return LevelName
     * @throws Throwable
     */
    public function getName(): LevelName
    {
        return $this->getModel()->getName();
    }

    /**
     * @return LevelDescription
     * @throws Throwable
     */
    public function getDescription(): LevelDescription
    {
        return $this->getModel()->getDescription();
    }

    /**
     * @return LevelAdvertisingText
     * @throws Throwable
     */
    public function getAdvertisingText(): LevelAdvertisingText
    {
        return $this->getModel()->getAdvertisingText();
    }

    /**
     * @return LevelId
     * @throws Throwable
     */
    public function getParentLevel(): LevelId
    {
        return $this->getModel()->getParentLevel();
    }

    /**
     * @return FirstLevelFlag
     * @throws Throwable
     */
    public function getIsFirst(): FirstLevelFlag
    {
        return $this->getModel()->getIsFirst();
    }

    /**
     * @return MapperInterface
     * @throws Throwable
     */
    public function getMapper(): MapperInterface
    {
        return $this->getModel()->getMapper();
    }

    /**
     * @return array
     * @throws Throwable
     */
    public function toArray(): array
    {
        return $this->getModel()->toArray();
    }
}
