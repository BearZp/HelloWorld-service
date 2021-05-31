<?php

namespace App\Model\Level;

use App\DataType\LevelId;
use Tools\collection\AbstractImmutableCollection;
use Tools\exception\NotInstanceOfException;

class LevelIdCollection extends AbstractImmutableCollection
{
    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        if (!$value instanceof LevelId) {
            throw new NotInstanceOfException(
                '$value',
                'LevelId',
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
        /** @var LevelId $item */
        foreach (parent::toArray() as $item) {
            $result[] = $item->toInteger();
        }
        return $result;
    }
}
