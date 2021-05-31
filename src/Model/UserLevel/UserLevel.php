<?php

namespace App\Model\UserLevel;

use App\DataType\LevelId;
use App\DataType\UserId;
use Tools\models\mappers\MapperInterface;

class UserLevel implements UserLevelInterface
{
    /** @var UserId */
    protected $userId;

    /** @var LevelId */
    protected $levelId;

    /** @var LevelId */
    protected $nextLevelId;

    /** @var UserLevelStatusEnum */
    protected $status;

    /** @var MapperInterface */
    protected $mapper;

    public function __construct(
        UserId $userId,
        LevelId $levelId,
        LevelId $nextLevelId,
        UserLevelStatusEnum $status,
        MapperInterface $mapper
    ) {
        $this->userId = $userId;
        $this->levelId = $levelId;
        $this->nextLevelId = $nextLevelId;
        $this->status = $status;
        $this->mapper = $mapper;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return LevelId
     */
    public function getLevelId(): LevelId
    {
        return $this->levelId;
    }

    /**
     * @return LevelId
     */
    public function getNextLevelId(): LevelId
    {
        return $this->nextLevelId;
    }

    /**
     * @return UserLevelStatusEnum
     */
    public function getStatus(): UserLevelStatusEnum
    {
        return $this->status;
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

    /**
     * @param UserLevelStatusEnum $statusEnum
     * @return UserLevelInterface
     */
    public function withStatus(UserLevelStatusEnum $statusEnum): UserLevelInterface
    {
        $model = clone $this;
        $model->status = $statusEnum;
        if ($model->nextLevelId->toInteger() == 0) {
            $model->nextLevelId = $model->levelId;
        }
        return $model;
    }
}
