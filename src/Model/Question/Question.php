<?php

namespace App\Model\Question;

use App\DataType\LevelId;
use App\DataType\QuestionDescription;
use App\DataType\QuestionId;
use App\DataType\QuestionTitle;
use App\DataType\SortOrder;
use App\Model\StatusEnum;
use Tools\models\mappers\MapperInterface;

class Question implements QuestionInterface
{
    /** @var QuestionId */
    protected $id;

    /** @var QuestionTitle */
    protected $questionTitle;

    /** @var QuestionDescription */
    protected $questionDescription;

    /** @var SortOrder */
    protected $sortOrder;

    /** @var QuestionTypeEnum */
    protected $type;

    /** @var StatusEnum */
    protected $status;

    /** @var MapperInterface */
    protected $mapper;

    public function __construct(
        QuestionId $id,
        QuestionTitle $questionTitle,
        QuestionDescription $questionDescription,
        SortOrder $sortOrder,
        QuestionTypeEnum $type,
        StatusEnum $status,
        MapperInterface $mapper
    ) {
        $this->id = $id;
        $this->questionTitle = $questionTitle;
        $this->questionDescription = $questionDescription;
        $this->sortOrder = $sortOrder;
        $this->type = $type;
        $this->status = $status;
        $this->mapper = $mapper;
    }

    /**
     * @return QuestionId
     */
    public function getId(): QuestionId
    {
        return $this->id;
    }

    /**
     * @return QuestionTitle
     */
    public function getQuestionTitle(): QuestionTitle
    {
        return $this->questionTitle;
    }

    /**
     * @return QuestionDescription
     */
    public function getQuestionDescription(): QuestionDescription
    {
        return $this->questionDescription;
    }

    /**
     * @return SortOrder
     */
    public function getSortOrder(): SortOrder
    {
        return $this->sortOrder;
    }

    /**
     * @return QuestionTypeEnum
     */
    public function getType(): QuestionTypeEnum
    {
        return $this->type;
    }

    /**
     * @return StatusEnum
     */
    public function getStatus(): StatusEnum
    {
        return $this->status;
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
