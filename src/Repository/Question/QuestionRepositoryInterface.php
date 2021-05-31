<?php

namespace App\Repository\Question;

use App\DataType\QuestionId;
use App\Model\Question\QuestionCollection;
use App\Model\Question\QuestionInterface;
use App\Repository\RepositoryInterface;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Doctrine\DBAL\Exception as DoctrineDBALException;

interface QuestionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param QuestionId $id
     * @return QuestionInterface
     * @throws DoctrineDBALException
     */
    public function getById(QuestionId $id): QuestionInterface;

    /**
     * @param QuestionInterface $question
     * @return QuestionInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(QuestionInterface $question): QuestionInterface;

    /**
     * @param QuestionInterface $question
     * @return QuestionInterface
     * @throws DoctrineDBALException
     */
    public function update(QuestionInterface $question): QuestionInterface;

    /**
     * @param QuestionId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(QuestionId $id): bool;

    /**
     * @return QuestionCollection
     */
    public function getAllQuestions(): QuestionCollection;
}
