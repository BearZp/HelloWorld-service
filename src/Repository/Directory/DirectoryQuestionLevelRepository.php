<?php

namespace App\Repository\Directory;

use App\DataType\DirectoryName;
use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\Model\Directory\DirectoryCollection;
use App\Model\Directory\DirectoryInterface;
use App\Model\Directory\DirectoryMapper;
use App\Model\Question\QuestionInterface;
use App\Repository\AbstractRepository;
use App\doctrine\pgsql\Connection;
use Exception;
use Doctrine\DBAL\Exception as DoctrineDBALException;

class DirectoryQuestionLevelRepository extends AbstractRepository implements DirectoryQuestionLevelRepositoryInterface
{
    private const TABLE = 'kyc_directory_question';

    /**
     * CountryRepository constructor.
     * @param Connection $connection
     * @param DirectoryMapper $mapper
     */
    public function __construct(Connection $connection, DirectoryMapper $mapper)
    {
        parent::__construct($connection, $mapper);
    }

    /**
     * @param QuestionInterface $question
     * @param DirectoryInterface $directory
     * @return bool
     * @throws DoctrineDBALException
     * @throws Exception
     */
    public function attachDirectoryToQuestion(QuestionInterface $question, DirectoryInterface $directory): bool
    {
        $query = $this->getBuilder()
            ->insert(self::TABLE)
            ->values([
                'question_id' => ':question_id',
                'directory_name' => ':directory_name',
            ])
            ->setParameter(':question_id', $question->getId()->toString())
            ->setParameter(':directory_name', $directory->getDirectoryName()->toString())
            ->execute();
        if ($query) {
            return true;
        }

        throw new Exception('Can`t save directory relation', 500);
    }

    /**
     * @param QuestionId $questionId
     * @return DirectoryName
     * @throws DoctrineDBALException
     */
    public function getDirectoryNameForQuestionId(QuestionId $questionId): DirectoryName
    {
        $query = $this->getBuilder()
            ->select('directory_name')
            ->from(self::TABLE)
            ->where('question_id = :question_id')
            ->setParameter(':question_id', $questionId->toString())
            ->execute();

        return new DirectoryName($query->fetchAssociative()['directory_name']);
    }

    /**
     * @param QuestionId $questionId
     * @return DirectoryCollection
     * @throws DoctrineDBALException
     */
    public function getDirectoriesForQuestionId(QuestionId $questionId): DirectoryCollection
    {
        $query = $this->getBuilder()
            ->select('d.*')
            ->from(self::TABLE, 'dq')
            ->innerJoin('dq', 'kyc_directory', 'd', 'dq.directory_name = d.directory_name')
            ->where('dq.question_id = :question_id')
            ->setParameter(':question_id', $questionId->toString())
            ->execute();

        return $this->buildCollection(DirectoryCollection::class, $query);
    }

    /**
     * @param QuestionId $questionId
     * @return bool
     * @throws DoctrineDBALException
     */
    public function detachDirectoryFromQuestionId(QuestionId $questionId): bool
    {
        $query = $this->getBuilder()
            ->delete(self::TABLE)
            ->where('question_id = :question_id')
            ->setParameter(':question_id', $questionId->toString())
            ->execute();

        return (bool) $query;
    }
}
