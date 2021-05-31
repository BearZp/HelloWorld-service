<?php

namespace App\Model\UserLevel;

use Tools\collection\AbstractImmutableCollection;
use Tools\exception\NotInstanceOfException;

class UserLevelCollection extends AbstractImmutableCollection
{
    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        if (!$value instanceof UserLevelInterface) {
            throw new NotInstanceOfException(
                '$value',
                'UserLevelInterface',
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
        /** @var UserLevelInterface $item */
        foreach (parent::toArray() as $item) {
            $result[] = $item->toArray();
        }
        return $result;
    }
}
