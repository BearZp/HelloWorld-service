<?php

namespace App\Component\UserLevel;

use App\DataType\LevelId;
use App\DataType\UserId;
use App\Model\UserLevel\UserLevelInterface;
use App\Model\UserLevel\UserLevelStatusEnum;
use App\Request\UserLevel\SetUserLevelRequest;

interface UserLevelComponentInterface
{
    /**
     * @param UserId $userId
     * @return UserLevelInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function getUserLevel(UserId $userId): UserLevelInterface;

    /**
     * @param SetUserLevelRequest $setUserLevelRequest
     * @return UserLevelInterface
     */
    public function setUserLevel(SetUserLevelRequest $setUserLevelRequest): UserLevelInterface;

    /**
     * @param UserId $userId
     * @param UserLevelStatusEnum $status
     * @return UserLevelInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function setUserLevelStatus(UserId $userId, UserLevelStatusEnum $status): UserLevelInterface;

    /**
     * @param UserId $userId
     * @return UserLevelInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function upgrade(UserId $userId): UserLevelInterface;


    /**
     * @param UserId $userId
     * @param LevelId|null $targetLevelId
     * @return UserLevelInterface
     */
    public function downgrade(UserId $userId, LevelId $targetLevelId = null): UserLevelInterface;
}
