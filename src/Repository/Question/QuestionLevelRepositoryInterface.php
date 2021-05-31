<?php

namespace App\Repository\Question;

use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\Model\Level\LevelCollection;
use App\Model\Question\QuestionCollection;

interface QuestionLevelRepositoryInterface
{
    /**
     * @param QuestionId $questionId
     * @return LevelCollection
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getLevelsForQuestion(QuestionId $questionId): LevelCollection;

    /**
     * @param LevelId $levelId
     * @return QuestionCollection
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getQuestionsForLevel(LevelId $levelId): QuestionCollection;

    /**
     * @param QuestionId $questionId
     * @param LevelId $levelId
     * @return bool
     */
    public function attachQuestionToLevel(QuestionId $questionId, LevelId $levelId): bool;

    /**
     * @param QuestionId $questionId
     * @param LevelId $levelId
     * @return bool
     */
    public function detachQuestionFromLevel(QuestionId $questionId, LevelId $levelId): bool;
}
