<?php

namespace App\Model\Answer;

use Tools\collection\AbstractImmutableCollection;
use Tools\exception\NotInstanceOfException;

class AnswerCollection extends AbstractImmutableCollection
{
    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        if (!$value instanceof AnswerInterface) {
            throw new NotInstanceOfException(
                '$value',
                'AnswerInterface',
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
        /** @var AnswerInterface $item */
        foreach (parent::toArray() as $item) {
            $result[] = $item->toArray();
        }
        return $result;
    }
}
