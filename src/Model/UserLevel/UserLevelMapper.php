<?php

namespace App\Model\UserLevel;

use App\DataType\LevelId;
use App\DataType\UserId;
use Tools\exception\NotInstanceOfException;
use Tools\models\mappers\MapperInterface;
use ReflectionException;

class UserLevelMapper implements MapperInterface
{
    /**
     * @param object $model
     * @return array
     */
    public function toArray(object $model): array
    {
        if (! $model instanceof UserLevelInterface) {
            throw new NotInstanceOfException(
                '$object',
                'UserLevelInterface',
                is_object($model) ? get_class($model) : gettype($model)
            );
        }

        return [
            'user_id' => $model->getUserId()->toString(),
            'level_id' => $model->getLevelId()->toInteger(),
            'next_level_id' => $model->getNextLevelId()->toInteger(),
            'next_level_status' => $model->getStatus()->getValue(),
        ];
    }

    /**
     * @param array $data
     * @return UserLevel
     * @throws ReflectionException
     */
    public function fromArray(array $data): UserLevel
    {
        return new UserLevel(
            new UserId($data['user_id']),
            new LevelId($data['level_id']),
            new LevelId($data['next_level_id'] ?? 0),
            new UserLevelStatusEnum((int) $data['next_level_status']),
            $this
        );
    }
}
