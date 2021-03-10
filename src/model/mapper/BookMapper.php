<?php

namespace App\model\mapper;

use App\model\Book;
use App\model\BookInterface;
use Lib\exception\NotInstanceOfException;
use Lib\models\mappers\MapperInterface;
use Lib\types\IntegerType;
use Lib\types\StringType255;

class BookMapper implements MapperInterface
{
    /**
     * @param object $model
     * @return array
     */
    public function toArray(object $model): array
    {
        if (! $model instanceof BookInterface) {
            throw new NotInstanceOfException(
                '$object',
                'BookInterface',
                is_object($model) ? get_class($model) : gettype($model)
            );
        }

        return [
            'id' => $model->getId()->toInteger(),
            'name' => $model->getName()->toString(),
            'pages' => $model->getPages()->toInteger()
        ];
    }

    /**
     * @param array $data
     * @return Book
     */
    public function fromArray(array $data): Book
    {
        return new Book(
            new IntegerType($data['id']),
            new StringType255($data['name']),
            new IntegerType($data['pages']),
            $this
        );
    }
}