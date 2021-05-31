<?php

namespace App\Repository\Answer;

use App\DataType\AnswerId;
use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\DataType\UserId;
use App\Model\Answer\AnswerCollection;
use App\Model\Answer\AnswerInterface;
use App\Model\Answer\AnswerStatusEnum;
use App\Repository\RepositoryInterface;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Doctrine\DBAL\Exception as DoctrineDBALException;

interface AnswerRepositoryInterface extends RepositoryInterface
{
    /**
     * @param AnswerId $id
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function getById(AnswerId $id): AnswerInterface;

    /**
     * @param QuestionId $questionId
     * @param UserId $userId
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function getAnswerForQuestion(QuestionId $questionId, UserId $userId): AnswerInterface;

    /**
     * @param UserId $userId
     * @return AnswerCollection
     * @throws DoctrineDBALException
     */
    public function getAnswersForUser(UserId $userId): AnswerCollection;

    /**
     * @param AnswerInterface $answer
     * @return AnswerInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(AnswerInterface $answer): AnswerInterface;

    /**
     * @param AnswerInterface $answer
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function update(AnswerInterface $answer): AnswerInterface;

    /**
     * @param AnswerId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(AnswerId $id): bool;
}
