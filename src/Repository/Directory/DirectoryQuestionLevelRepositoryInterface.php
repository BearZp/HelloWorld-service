<?php

namespace App\Repository\Directory;

use App\DataType\DirectoryName;
use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\Model\Directory\DirectoryCollection;
use App\Model\Directory\DirectoryInterface;
use App\Model\Question\QuestionInterface;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Exception;

interface DirectoryQuestionLevelRepositoryInterface
{
    /**
     * @param QuestionInterface $question
     * @param DirectoryInterface $directory
     * @return bool
     * @throws DoctrineDBALException
     * @throws Exception
     */
    public function attachDirectoryToQuestion(QuestionInterface $question, DirectoryInterface $directory): bool;

    /**
     * @param QuestionId $questionId
     * @return DirectoryName
     * @throws DoctrineDBALException
     */
    public function getDirectoryNameForQuestionId(QuestionId $questionId): DirectoryName;


    /**
     * @param QuestionId $questionId
     * @return DirectoryCollection
     * @throws DoctrineDBALException
     */
    public function getDirectoriesForQuestionId(QuestionId $questionId): DirectoryCollection;

    /**
     * @param QuestionId $questionId
     * @return bool
     * @throws DoctrineDBALException
     */
    public function detachDirectoryFromQuestionId(QuestionId $questionId): bool;
}
