<?php

namespace App\Model\Level;

use App\DataType\FirstLevelFlag;
use App\DataType\LevelAdvertisingText;
use App\DataType\LevelDescription;
use App\DataType\LevelId;
use App\DataType\LevelName;
use Tools\models\mappers\MapperInterface;

class Level implements LevelInterface
{
    /** @var LevelId */
    protected $id;

    /** @var LevelName */
    protected $name;

    /** @var LevelDescription */
    protected $description;

    /** @var LevelAdvertisingText */
    protected $advertisingText;

    /** @var LevelId */
    protected $parentLevel;

    /** @var FirstLevelFlag */
    protected $isFirst;

    /** @var MapperInterface */
    protected $mapper;

    /**
     * Country constructor.
     * @param LevelId $id
     * @param LevelName $name
     * @param LevelDescription $description
     * @param LevelAdvertisingText $advertisingText
     * @param LevelId $parentLevel
     * @param FirstLevelFlag $isFirst
     * @param MapperInterface $mapper
     */
    public function __construct(
        LevelId $id,
        LevelName $name,
        LevelDescription $description,
        LevelAdvertisingText $advertisingText,
        LevelId $parentLevel,
        FirstLevelFlag $isFirst,
        MapperInterface $mapper
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->advertisingText = $advertisingText;
        $this->parentLevel = $parentLevel;
        $this->isFirst = $isFirst;
        $this->mapper = $mapper;
    }

    /**
     * @return LevelId
     */
    public function getId(): LevelId
    {
        return $this->id;
    }

    /**
     * @return LevelName
     */
    public function getName(): LevelName
    {
        return $this->name;
    }

    /**
     * @return LevelDescription
     */
    public function getDescription(): LevelDescription
    {
        return $this->description;
    }

    /**
     * @return LevelAdvertisingText
     */
    public function getAdvertisingText(): LevelAdvertisingText
    {
        return $this->advertisingText;
    }

    /**
     * @return LevelId
     */
    public function getParentLevel(): LevelId
    {
        return $this->parentLevel;
    }

    /**
     * @return FirstLevelFlag
     */
    public function getIsFirst(): FirstLevelFlag
    {
        return $this->isFirst;
    }

    /**
     * @return MapperInterface
     */
    public function getMapper(): MapperInterface
    {
        return $this->mapper;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->mapper->toArray($this);
    }
}
