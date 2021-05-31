<?php

namespace App\Model\Answer;

use App\DataType\AnswerId;
use App\DataType\AnswerValue;
use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\DataType\UserId;
use Tools\types\TextType;
use Tools\types\UuidType;
use Tools\exception\NotInstanceOfException;
use Tools\models\mappers\MapperInterface;
use Tools\types\IntegerType;
use Tools\types\StringType255;
use ReflectionException;

class AnswerMapper implements MapperInterface
{
    /**
     * @param object $model
     * @return array
     */
    public function toArray(object $model): array
    {
        if (! $model instanceof AnswerInterface) {
            throw new NotInstanceOfException(
                '$object',
                'AnswerInterface',
                is_object($model) ? get_class($model) : gettype($model)
            );
        }

        return [
            'id' => $model->getId()->toString(),
            'question_id' => $model->getQuestionId()->toString(),
            'user_id' => $model->getUserId()->toString(),
            'type' => $model->getType()->getValue(),
            'status' => $model->getStatus()->getValue(),
            'value' => $model->getValue()->toString(),
        ];
    }

    /**
     * @param array $data
     * @return Answer
     * @throws ReflectionException
     */
    public function fromArray(array $data): Answer
    {
        return new Answer(
            new AnswerId($data['id']),
            new QuestionId($data['question_id']),
            new UserId($data['user_id']),
            new AnswerTypeEnum((int) $data['type']),
            new AnswerStatusEnum((int) $data['status']),
            new AnswerValue($data['value']),
            $this
        );
    }
}
