<?php

namespace App\model\collection;

use App\Entity\City;
use App\model\Country;
use Lib\collection\AbstractImmutableCollection;
use Lib\exception\NotInstanceOfException;

class CityCollection extends AbstractImmutableCollection
{
    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        if (!$value instanceof City) {
            throw new NotInstanceOfException(
                '$value',
                'City',
                is_object($value) ? get_class($value) : gettype($value)
            );
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        /** @var City $city */
        foreach (parent::toArray() as $city) {
            $result[] = ['id' => $city->id, 'name' => $city->name, 'population' => $city->population];
        }
        return $result;
    }
}