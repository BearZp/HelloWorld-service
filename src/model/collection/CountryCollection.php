<?php

namespace App\model\collection;

use App\model\Country;
use Lib\collection\AbstractImmutableCollection;
use Lib\exception\NotInstanceOfException;

class CountryCollection extends AbstractImmutableCollection
{
    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        if (!$value instanceof Country) {
            throw new NotInstanceOfException(
                '$value',
                'Country',
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