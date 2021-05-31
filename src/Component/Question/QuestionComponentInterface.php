<?php

namespace App\Component\Question;

use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\Model\Level\LevelCollection;
use App\Model\Question\QuestionCollection;
use App\Model\Question\QuestionInterface;
use App\Request\Question\CreateQuestionRequest;
use App\Request\Question\UpdateQuestionRequest;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Doctrine\DBAL\Exception as DoctrineDBALException;

interface QuestionComponentInterface
{
    /**
     * @param QuestionId $id
     * @return array
     * @throws DoctrineDBALException
     */
    public function getByIdWithDirectory(QuestionId $id): array;

    /**
     * @param QuestionId $id
     * @return QuestionInterface
     * @throws DoctrineDBALException
     */
    public function getById(QuestionId $id): QuestionInterface;

    /**
     * @param LevelId $levelId
     * @return array
     * @throws DoctrineDBALException
     */
    public function getQuestionsForLevel(LevelId $levelId): array;

    /**
     * @param QuestionId $questionId
     * @return LevelCollection
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getLevelCollectionForQuestion(QuestionId $questionId): LevelCollection;

    /**
     * @param LevelId $levelId
     * @return QuestionCollection
     * @throws DoctrineDBALException
     */
    public function getQuestionCollectionForLevel(LevelId $levelId): QuestionCollection;

    /**
     * @param CreateQuestionRequest $questionRequest
     * @return QuestionInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(CreateQuestionRequest $questionRequest): QuestionInterface;

    /**
     * @param UpdateQuestionRequest $questionRequest
     * @return QuestionInterface
     * @throws DoctrineDBALException
     */
    public function update(UpdateQuestionRequest $questionRequest): QuestionInterface;

    /**
     * @param QuestionId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(QuestionId $id): bool;
}