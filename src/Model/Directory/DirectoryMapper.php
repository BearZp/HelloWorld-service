<?php

namespace App\Model\Directory;

use App\DataType\DirectoryId;
use App\DataType\DirectoryName;
use App\DataType\DirectoryValue;
use App\DataType\SortOrder;
use App\Model\StatusEnum;
use Tools\exception\NotInstanceOfException;
use Tools\models\mappers\MapperInterface;
use ReflectionException;

class DirectoryMapper implements MapperInterface
{
    /**
     * @param object $model
     * @return array
     */
    public function toArray(object $model): array
    {
        if (! $model instanceof DirectoryInterface) {
            throw new NotInstanceOfException(
                '$object',
                'DirectoryInterface',
                is_object($model) ? get_class($model) : gettype($model)
            );
        }

        return [
            'id' => $model->getId()->toInteger(),
            'directory_name' => $model->getDirectoryName()->toString(),
            'directory_value' => $model->getDirectoryValue()->toString(),
            'sort_order' => $model->getSortOrder()->toInteger(),
            'status' => $model->getStatus()->getValue(),
        ];
    }

    /**
     * @param array $data
     * @return Directory
     * @throws ReflectionException
     */
    public function fromArray(array $data): Directory
    {
        return new Directory(
            new DirectoryId((int) $data['id']),
            new DirectoryName($data['directory_name']),
            new DirectoryValue($data['directory_value']),
            new SortOrder((int) $data['sort_order']),
            new StatusEnum((int) $data['status']),
            $this
        );
    }
}
