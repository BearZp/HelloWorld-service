<?php

namespace App\model\mapper;

use App\model\Country;
use App\model\CountryInterface;
use Lib\exception\NotInstanceOfException;
use Lib\models\mappers\MapperInterface;
use Lib\types\IntegerType;
use Lib\types\StringType255;

class CountryMapper implements MapperInterface
{
    /**
     * @param object $model
     * @return array
     */
    public function toArray(object $model): array
    {
        if (! $model instanceof CountryInterface) {
            throw new NotInstanceOfException(
                '$object',
                'CountryInterface',
                is_object($model) ? get_class($model) : gettype($model)
            );
        }

        return [
            'id' => $model->getId()->toInteger(),
            'name' => $model->getName()->toString(),
            'population' => $model->getPopulation()->toInteger()
        ];
    }

    /**
     * @param array $data
     * @return Country
     */
    public function fromArray(array $data): Country
    {
        return new Country(
            new IntegerType($data['id']),
            new StringType255($data['name']),
            new IntegerType($data['population']),
            $this
        );
    }
}