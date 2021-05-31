<?php

namespace App\Model\UserLevel;

use App\DataType\LevelId;
use App\DataType\UserId;
use Tools\models\FutureObjectInterface;
use Tools\models\FutureObjectTrait;
use Tools\models\mappers\MapperInterface;
use Throwable;

class UserLevelFuture implements UserLevelInterface, FutureObjectInterface
{
    use FutureObjectTrait {
        FutureObjectTrait::get as getModel;
    }

    /**
     * @return UserId
     * @throws Throwable
     */
    public function getUserId(): UserId
    {
        return $this->getModel()->getUserId();
    }

    /**
     * @return LevelId
     * @throws Throwable
     */
    public function getLevelId(): LevelId
    {
        return $this->getModel()->getLevelId();
    }

    /**
     * @return LevelId
     * @throws Throwable
     */
    public function getNextLevelId(): LevelId
    {
        return $this->getModel()->getNextLevelId();
    }

    /**
     * @return UserLevelStatusEnum
     * @throws Throwable
     */
    public function getStatus(): UserLevelStatusEnum
    {
        return $this->getModel()->getStatus();
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

    /**
     * @param UserLevelStatusEnum $statusEnum
     * @return UserLevelInterface
     * @throws Throwable
     */
    public function withStatus(UserLevelStatusEnum $statusEnum): UserLevelInterface
    {
        return $this->getModel()->withStatus($statusEnum);
    }
}
