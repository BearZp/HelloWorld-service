<?php

namespace App\Component\Directory;

use App\DataType\DirectoryId;
use App\DataType\DirectoryName;
use App\DataType\QuestionId;
use App\Model\Directory\DirectoryCollection;
use App\Model\Directory\DirectoryInterface;
use App\Model\Question\QuestionInterface;
use App\Request\Directory\CreateDirectoryRequest;
use App\Request\Directory\UpdateDirectoryRequest;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Psr\Cache\InvalidArgumentException;
use Throwable;

interface DirectoryComponentInterface
{
    /**
     * @param DirectoryId $id
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     */
    public function getById(DirectoryId $id): DirectoryInterface;

    /**
     * @param DirectoryName $directoryName
     * @return DirectoryCollection
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function getDirectoryByName(DirectoryName $directoryName): DirectoryCollection;

    /**
     * @param QuestionId $questionId
     * @return DirectoryCollection
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function getDirectoryForQuestion(QuestionId $questionId): DirectoryCollection;

    /**
     * @param CreateDirectoryRequest $createDirectoryRequest
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(CreateDirectoryRequest $createDirectoryRequest): DirectoryInterface;

    /**
     * @param UpdateDirectoryRequest $updateDirectoryRequest
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     */
    public function update(UpdateDirectoryRequest $updateDirectoryRequest): DirectoryInterface;

    /**
     * @param DirectoryId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(DirectoryId $id): bool;

    /**
     * @return array
     * @throws DoctrineDBALException
     */
    public function getList(): array;

    /**
     * @param DirectoryInterface $directory
     * @param QuestionInterface $question
     * @return bool
     * @throws DoctrineDBALException
     */
    public function attachDirectoryToQuestion(DirectoryInterface $directory, QuestionInterface $question): bool;

    /**
     * @param QuestionId $questionId
     * @return bool
     * @throws DoctrineDBALException
     */
    public function detachDirectoryFromQuestionId(QuestionId $questionId): bool;
}
