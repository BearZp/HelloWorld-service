<?php

namespace App\Repository\Question;

use App\DataType\QuestionId;
use App\doctrine\pgsql\Connection;
use App\Model\Question\QuestionCollection;
use App\Model\Question\QuestionFuture;
use App\Model\Question\QuestionInterface;
use App\Model\Question\QuestionMapper;
use App\Repository\AbstractRepository;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Exception;

class QuestionRepository extends AbstractRepository implements QuestionRepositoryInterface
{
    protected const TABLE = 'kyc_question';

    public const COLLECTION_CLASS = QuestionCollection::class;

    /**
     * CountryRepository constructor.
     * @param Connection $connection
     * @param QuestionMapper $mapper
     */
    public function __construct(Connection $connection, QuestionMapper $mapper)
    {
        parent::__construct($connection, $mapper);
    }

    /**
     * @param QuestionId $id
     * @return QuestionInterface
     * @throws DoctrineDBALException
     */
    public function getById(QuestionId $id): QuestionInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter(':id', $id->toString())
            ->execute();

        return new QuestionFuture(function () use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @param QuestionInterface $question
     * @return QuestionInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(QuestionInterface $question): QuestionInterface
    {
        $query = $this->getBuilder()
            ->insert(self::TABLE)
            ->values([
                'id' => ':id',
                'question_title' => ':question_title',
                'question_description' => ':question_description',
                'sort_order' => ':sort_order',
                'type' => ':type',
                'status' => ':status',
            ])
            ->setParameter(':id', $question->getId()->toString())
            //->setParameter(':id', 1)
            ->setParameter(':question_title', $question->getQuestionTitle()->toString())
            ->setParameter(':question_description', $question->getQuestionDescription()->toString())
            ->setParameter(':sort_order', $question->getSortOrder()->toInteger())
            ->setParameter(':type', $question->getType()->getValue())
            ->setParameter(':status', $question->getStatus()->getValue())
            ->execute();
        if ($query) {
            return $this->getById($question->getId());
        }

        throw new Exception('Can`t save Question', 500);
    }

    /**
     * @param QuestionInterface $question
     * @return QuestionInterface
     * @throws DoctrineDBALException
     */
    public function update(QuestionInterface $question): QuestionInterface
    {
        $query = $this->getBuilder()
            ->update(self::TABLE)
            ->set('question_title', ':question_title')
            ->set('question_description', ':question_description')
            ->set('sort_order', ':sort_order')
            ->set('type', ':type')
            ->set('status', ':status')
            ->where('id = :id')
            ->setParameter(':id', $question->getId()->toString())
            ->setParameter(':question_title', $question->getQuestionTitle()->toString())
            ->setParameter(':question_description', $question->getQuestionDescription()->toString())
            ->setParameter(':sort_order', $question->getSortOrder()->toInteger())
            ->setParameter(':type', $question->getType()->getValue())
            ->setParameter(':status', $question->getStatus()->getValue())
            ->execute();

        if ($query) {
            return $question;
        }

        throw new Exception('Can`t update Question', 500);
    }

    /**
     * @param QuestionId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(QuestionId $id): bool
    {
        $query = $this->getBuilder()
            ->delete(self::TABLE)
            ->where('id = :id')
            ->setParameter(':id', $id->toString())
            ->execute();

        return (bool) $query;
    }

    /**
     * @return QuestionCollection
     * @throws DoctrineDBALException
     */
    public function getAllQuestions(): QuestionCollection
    {
        /** @var QuestionCollection $result */
        $result = parent::getAll(QuestionCollection::class);
        return $result;
    }
}
