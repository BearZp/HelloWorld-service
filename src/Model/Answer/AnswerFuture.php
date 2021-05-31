<?php

namespace App\Model\Answer;

use App\DataType\AnswerId;
use App\DataType\AnswerValue;
use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\DataType\UserId;
use Tools\models\FutureObjectInterface;
use Tools\models\FutureObjectTrait;
use Tools\models\mappers\MapperInterface;

class AnswerFuture implements AnswerInterface, FutureObjectInterface
{
    use FutureObjectTrait {
        FutureObjectTrait::get as getModel;
    }

    /**
     * @return AnswerId
     * @throws \Throwable
     */
    public function getId(): AnswerId
    {
        return $this->getModel()->getId();
    }

    /**
     * @return QuestionId
     * @throws \Throwable
     */
    public function getQuestionId(): QuestionId
    {
        return $this->getModel()->getQuestionId();
    }

    /**
     * @return UserId
     * @throws \Throwable
     */
    public function getUserId(): UserId
    {
        return $this->getModel()->getUserId();
    }

    /**
     * @return AnswerValue
     * @throws \Throwable
     */
    public function getValue(): AnswerValue
    {
        return $this->getModel()->getValue();
    }

    /**
     * @return AnswerTypeEnum
     * @throws \Throwable
     */
    public function getType(): AnswerTypeEnum
    {
        return $this->getModel()->getType();
    }

    /**
     * @return AnswerStatusEnum
     * @throws \Throwable
     */
    public function getStatus(): AnswerStatusEnum
    {
        return $this->getModel()->getStatus();
    }

    /**
     * @return MapperInterface
     * @throws \Throwable
     */
    public function getMapper(): MapperInterface
    {
        return $this->getModel()->getMapper();
    }

    /**
     * @return array
     * @throws \Throwable
     */
    public function toArray(): array
    {
        return $this->getModel()->toArray();
    }
}
