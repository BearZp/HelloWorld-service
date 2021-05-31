<?php

namespace App\Model\Question;

use Tools\collection\AbstractImmutableCollection;
use Tools\exception\NotInstanceOfException;

class QuestionCollection extends AbstractImmutableCollection
{
    /**
     * @param mixed $value
     */
    public function validate($value): void
    {
        if (!$value instanceof QuestionInterface) {
            throw new NotInstanceOfException(
                '$value',
                'QuestionInterface',
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
        /** @var QuestionInterface $item */
        foreach (parent::toArray() as $item) {
            $result[] = $item->toArray();
        }
        return $result;
    }
}
