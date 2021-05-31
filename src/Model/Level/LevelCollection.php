<?php

namespace App\Model\Level;

use Tools\collection\AbstractImmutableCollection;
use Tools\exception\NotInstanceOfException;

class LevelCollection extends AbstractImmutableCollection
{
    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        if (!$value instanceof LevelInterface) {
            throw new NotInstanceOfException(
                '$value',
                'LevelInterface',
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
        /** @var LevelInterface $item */
        foreach (parent::toArray() as $item) {
            $result[] = $item->toArray();
        }
        return $result;
    }
}
