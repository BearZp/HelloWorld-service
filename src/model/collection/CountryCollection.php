<?php

namespace App\model\collection;

use App\model\CountryInterface;
use Lib\collection\AbstractImmutableCollection;
use Lib\exception\NotInstanceOfException;

class CountryCollection extends AbstractImmutableCollection
{
    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        if (!$value instanceof CountryInterface) {
            throw new NotInstanceOfException(
                '$value',
                'CountryInterface',
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
        /** @var Country $country */
        foreach (parent::toArray() as $country) {
            $result[] = $country->toArray();
        }
        return $result;
    }
}