<?php

namespace App\Model\Question;

use App\DataType\LevelId;
use App\DataType\QuestionDescription;
use App\DataType\QuestionId;
use App\DataType\QuestionTitle;
use App\DataType\SortOrder;
use App\Model\StatusEnum;
use Tools\models\ModelInterface;

interface QuestionInterface extends ModelInterface
{
    /**
     * @return QuestionId
     */
    public function getId(): QuestionId;

    /**
     * @return QuestionTitle
     */
    public function getQuestionTitle(): QuestionTitle;

    /**
     * @return QuestionDescription
     */
    public function getQuestionDescription(): QuestionDescription;

    /**
     * @return SortOrder
     */
    public function getSortOrder(): SortOrder;

    /**
     * @return QuestionTypeEnum
     */
    public function getType(): QuestionTypeEnum;

    /**
     * @return StatusEnum
     */
    public function getStatus(): StatusEnum;
}
