<?php

namespace App\Model\Answer;

use App\DataType\AnswerId;
use App\DataType\AnswerValue;
use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\DataType\UserId;
use Tools\models\mappers\MapperInterface;

class Answer implements AnswerInterface
{
    /** @var AnswerId */
    protected $id;

    /** @var QuestionId */
    protected $questionId;

    /** @var UserId */
    protected $userId;

    /** @var AnswerTypeEnum */
    protected $type;

    /** @var AnswerStatusEnum */
    protected $status;

    /** @var AnswerValue */
    protected $value;

    /** @var MapperInterface */
    protected $mapper;

    public function __construct(
        AnswerId $id,
        QuestionId $questionId,
        UserId $userId,
        AnswerTypeEnum $type,
        AnswerStatusEnum $status,
        AnswerValue $value,
        MapperInterface $mapper
    ) {

        $this->id = $id;
        $this->questionId = $questionId;
        $this->userId = $userId;
        $this->type = $type;
        $this->status = $status;
        $this->value = $value;
        $this->mapper = $mapper;
    }

    /**
     * @return AnswerId
     */
    public function getId(): AnswerId
    {
        return $this->id;
    }

    /**
     * @return QuestionId
     */
    public function getQuestionId(): QuestionId
    {
        return $this->questionId;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return AnswerTypeEnum
     */
    public function getType(): AnswerTypeEnum
    {
        return $this->type;
    }

    /**
     * @return AnswerStatusEnum
     */
    public function getStatus(): AnswerStatusEnum
    {
        return $this->status;
    }

    /**
     * @return AnswerValue
     */
    public function getValue(): AnswerValue
    {
        return $this->value;
    }

    /**
     * @return MapperInterface
     */
    public function getMapper(): MapperInterface
    {
        return $this->mapper;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->mapper->toArray($this);
    }
}
