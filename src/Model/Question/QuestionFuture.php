<?php

namespace App\Model\Question;

use App\DataType\LevelId;
use App\DataType\QuestionDescription;
use App\DataType\QuestionId;
use App\DataType\QuestionTitle;
use App\DataType\SortOrder;
use App\Model\StatusEnum;
use Tools\models\FutureObjectInterface;
use Tools\models\FutureObjectTrait;
use Tools\models\mappers\MapperInterface;
use Throwable;

class QuestionFuture implements QuestionInterface, FutureObjectInterface
{
    use FutureObjectTrait {
        FutureObjectTrait::get as getModel;
    }

    /**
     * @return QuestionId
     * @throws Throwable
     */
    public function getId(): QuestionId
    {
        return $this->getModel()->getId();
    }

    /**
     * @return QuestionTitle
     * @throws Throwable
     */
    public function getQuestionTitle(): QuestionTitle
    {
        return $this->getModel()->getQuestionTitle();
    }

    /**
     * @return QuestionDescription
     * @throws Throwable
     */
    public function getQuestionDescription(): QuestionDescription
    {
        return $this->getModel()->getQuestionDescription();
    }

    /**
     * @return SortOrder
     * @throws Throwable
     */
    public function getSortOrder(): SortOrder
    {
        return $this->getModel()->getSortOrder();
    }

    /**
     * @return QuestionTypeEnum
     * @throws Throwable
     */
    public function getType(): QuestionTypeEnum
    {
        return $this->getModel()->getType();
    }

    /**
     * @return StatusEnum
     * @throws Throwable
     */
    public function getStatus(): StatusEnum
    {
        return $this->getModel()->getStatus();
    }

    /**
     * @return MapperInterface
     * @throws Throwable
     */
    public function getMapper(): MapperInterface
    {
        return $this->getModel()->getMapper();
    }

    /**
     * @return array
     * @throws Throwable
     */
    public function toArray(): array
    {
        return $this->getModel()->toArray();
    }
}
