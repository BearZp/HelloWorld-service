<?php

namespace App\Model\Question;

use App\DataType\LevelId;
use App\DataType\QuestionDescription;
use App\DataType\QuestionId;
use App\DataType\QuestionTitle;
use App\DataType\SortOrder;
use App\Model\StatusEnum;
use Tools\types\UuidType;
use Tools\exception\NotInstanceOfException;
use Tools\models\mappers\MapperInterface;
use Tools\types\IntegerType;
use Tools\types\StringType255;
use ReflectionException;

class QuestionMapper implements MapperInterface
{
    /**
     * @param object $model
     * @return array
     */
    public function toArray(object $model): array
    {
        if (! $model instanceof QuestionInterface) {
            throw new NotInstanceOfException(
                '$object',
                'QuestionInterface',
                is_object($model) ? get_class($model) : gettype($model)
            );
        }

        return [
            'id' => $model->getId()->toString(),
            'question_title' => $model->getQuestionTitle()->toString(),
            'question_description' => $model->getQuestionDescription()->toString(),
            'sort_order' => $model->getSortOrder()->toInteger(),
            'type' => $model->getType()->getValue(),
            'status' => $model->getStatus()->getValue(),
        ];
    }

    /**
     * @param array $data
     * @return Question
     * @throws ReflectionException
     */
    public function fromArray(array $data): Question
    {
        return new Question(
            new QuestionId($data['id']),
            new QuestionTitle($data['question_title']),
            new QuestionDescription($data['question_description']),
            new SortOrder((int) $data['sort_order']),
            new QuestionTypeEnum((int) $data['type']),
            new StatusEnum((int) $data['status']),
            $this
        );
    }
}
