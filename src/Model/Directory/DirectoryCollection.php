<?php

namespace App\Model\Directory;

use Tools\collection\AbstractImmutableCollection;
use Tools\exception\NotInstanceOfException;

class DirectoryCollection extends AbstractImmutableCollection
{
    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        if (!$value instanceof DirectoryInterface) {
            throw new NotInstanceOfException(
                '$value',
                'DirectoryInterface',
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
        /** @var DirectoryInterface $item */
        foreach (parent::toArray() as $item) {
            $result[] = $item->toArray();
        }
        return $result;
    }

    /**
     * @return array
     */
    public function toList(): array
    {
        $result = [];
        $name = null;
        /** @var DirectoryInterface $item */
        foreach (parent::toArray() as $item) {
            $name = $item->getDirectoryName()->toString();
            $result[$name][$item->getId()->toString()] = $item->getDirectoryValue()->toString();
        }
        return $result;
    }
}
