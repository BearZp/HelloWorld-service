<?php

namespace App\Model\Level;

use App\DataType\FirstLevelFlag;
use App\DataType\LevelAdvertisingText;
use App\DataType\LevelDescription;
use App\DataType\LevelId;
use App\DataType\LevelName;
use Tools\types\BooleanType;
use Tools\exception\NotInstanceOfException;
use Tools\models\mappers\MapperInterface;
use Tools\types\IntegerType;
use Tools\types\StringType255;
use Tools\types\TextType;

class LevelMapper implements MapperInterface
{
    /**
     * @param object $model
     * @return array
     */
    public function toArray(object $model): array
    {
        if (! $model instanceof LevelInterface) {
            throw new NotInstanceOfException(
                '$object',
                'LevelInterface',
                is_object($model) ? get_class($model) : gettype($model)
            );
        }

        return [
            'id' => $model->getId()->toInteger(),
            'name' => $model->getName()->toString(),
            'description' => $model->getDescription()->toString(),
            'advertising_text' => $model->getAdvertisingText()->toString(),
            'parent_level_id' => $model->getParentLevel()->toInteger(),
            'is_first' => $model->getIsFirst()->toString(),
        ];
    }

    /**
     * @param array $data
     * @return Level
     */
    public function fromArray(array $data): Level
    {
        $isFirst = $data['is_first'] === 'true' || $data['is_first'] === 't';
        return new Level(
            new LevelId($data['id']),
            new LevelName($data['name']),
            new LevelDescription($data['description']),
            new LevelAdvertisingText($data['advertising_text']),
            new LevelId($data['parent_level_id']),
            new FirstLevelFlag($isFirst),
            $this
        );
    }
}
