<?php

namespace App\Model\UserLevel;

use App\DataType\LevelId;
use App\DataType\UserId;
use Tools\models\ModelInterface;

interface UserLevelInterface extends ModelInterface
{
    /**
     * @return UserId
     */
    public function getUserId(): UserId;

    /**
     * @return LevelId
     */
    public function getLevelId(): LevelId;

    /**
     * @return LevelId
     */
    public function getNextLevelId(): LevelId;

    /**
     * @return UserLevelStatusEnum
     */
    public function getStatus(): UserLevelStatusEnum;

    /**
     * @param UserLevelStatusEnum $statusEnum
     * @return UserLevelInterface
     */
    public function withStatus(UserLevelStatusEnum $statusEnum): UserLevelInterface;
}
