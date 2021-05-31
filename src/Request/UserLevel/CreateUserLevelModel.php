<?php

namespace App\Request\UserLevel;

use App\DataType\LevelId;
use App\DataType\NextLevelId;
use App\DataType\UserId;
use App\Model\UserLevel\UserLevelStatusEnum;

class CreateUserLevelModel
{
    /** @var UserId */
    protected $userId;

    /** @var LevelId */
    protected $levelId;

    /** @var LevelId */
    protected $nextLevelId;

    /** @var UserLevelStatusEnum */
    protected $status;

    /**
     * CreateUserLevelModel constructor.
     * @param UserId $userId
     * @param LevelId $levelId
     * @param LevelId $nextLevelId
     * @param UserLevelStatusEnum $status
     */
    public function __construct(
        UserId $userId,
        LevelId $levelId,
        LevelId $nextLevelId,
        UserLevelStatusEnum $status
    ) {
        $this->userId = $userId;
        $this->levelId = $levelId;
        $this->nextLevelId = $nextLevelId;
        $this->status = $status;
    }

    /** @return UserId */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /** @return LevelId */
    public function getLevelId(): LevelId
    {
        return $this->levelId;
    }

    /** @return LevelId */
    public function getNextLevelId(): LevelId
    {
        return $this->nextLevelId;
    }

    /** @return UserLevelStatusEnum */
    public function getStatus(): UserLevelStatusEnum
    {
        return $this->status;
    }
}
