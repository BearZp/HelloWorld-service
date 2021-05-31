<?php

namespace App\Component\Answer;

use App\DataType\AnswerId;
use App\Model\Answer\AnswerInterface;
use App\Request\Answer\ByUserAndLevelRequest;
use App\Request\Answer\CreateAnswerRequest;
use App\Request\Answer\UpdateAnswerRequest;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Doctrine\DBAL\Exception as DoctrineDBALException;

interface AnswerComponentInterface
{
    /**
     * @param AnswerId $id
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function getById(AnswerId $id): AnswerInterface;

    /**
     * @param CreateAnswerRequest $request
     * @return AnswerInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(CreateAnswerRequest $request): AnswerInterface;

    /**
     * @param UpdateAnswerRequest $request
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function update(UpdateAnswerRequest $request): AnswerInterface;

    /**
     * @param AnswerId $id
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function validateAnswer(AnswerId $id): AnswerInterface;

    /**
     * @param AnswerId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(AnswerId $id): bool;

    /**
     * @param ByUserAndLevelRequest $request
     * @return array
     */
    public function getAnswerListForValidate(ByUserAndLevelRequest $request): array;
}
