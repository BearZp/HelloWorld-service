<?php

namespace App\model\collection;

use App\model\BookInterface;
use Lib\collection\AbstractImmutableCollection;
use Lib\exception\NotInstanceOfException;

class BookCollection extends AbstractImmutableCollection
{
    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        if (!$value instanceof BookInterface) {
            throw new NotInstanceOfException(
                '$value',
                'BookInterface',
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
        /** @var BookInterface $country */
        foreach (parent::toArray() as $country) {
            $result[] = $country->toArray();
        }
        return $result;
    }
}