<?php

namespace App\Model\Answer;

use App\DataType\AnswerId;
use App\DataType\AnswerValue;
use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\DataType\UserId;
use Tools\models\ModelInterface;

interface AnswerInterface extends ModelInterface
{
    /**
     * @return AnswerId
     */
    public function getId(): AnswerId;

    /**
     * @return QuestionId
     */
    public function getQuestionId(): QuestionId;

    /**
     * @return UserId
     */
    public function getUserId(): UserId;

    /**
     * @return AnswerTypeEnum
     */
    public function getType(): AnswerTypeEnum;

    /**
     * @return AnswerStatusEnum
     */
    public function getStatus(): AnswerStatusEnum;

    /**
     * @return AnswerValue
     */
    public function getValue(): AnswerValue;
}
