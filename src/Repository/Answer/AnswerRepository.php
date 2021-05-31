<?php

namespace App\Repository\Answer;

use App\DataType\AnswerId;
use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\DataType\UserId;
use App\doctrine\pgsql\Connection;
use App\Model\Answer\AnswerCollection;
use App\Model\Answer\AnswerFuture;
use App\Model\Answer\AnswerInterface;
use App\Model\Answer\AnswerMapper;
use App\Model\Answer\AnswerStatusEnum;
use App\Repository\AbstractRepository;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Exception;

class AnswerRepository extends AbstractRepository implements AnswerRepositoryInterface
{
    private const TABLE = 'kyc_answer';

    /**
     * CountryRepository constructor.
     * @param Connection $connection
     * @param AnswerMapper $mapper
     */
    public function __construct(Connection $connection, AnswerMapper $mapper)
    {
        parent::__construct($connection, $mapper);
    }

    /**
     * @param AnswerId $id
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function getById(AnswerId $id): AnswerInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter(':id', $id->toString())
            ->execute();

        return new AnswerFuture(function () use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @param QuestionId $questionId
     * @param UserId $userId
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function getAnswerForQuestion(QuestionId $questionId, UserId $userId): AnswerInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('question_id = :question_id')
            ->andWhere('user_id = :user_id')
            ->setParameter(':question_id', $questionId->toString())
            ->setParameter(':user_id', $userId->toString())
            ->execute();

        return new AnswerFuture(function () use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @param UserId $userId
     * @return AnswerCollection
     * @throws DoctrineDBALException
     */
    public function getAnswersForUser(UserId $userId): AnswerCollection
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('user_id = :user_id')
            ->setParameter(':user_id', $userId->toString())
            ->execute();

        return new AnswerCollection(function () use ($query) {
            $array = [];
            foreach ($query->fetchAllAssociative() as $item) {
                $array[] = $this->mapper->fromArray($item);
            }
            return $array;
        });
    }

    /**
     * @param AnswerInterface $answer
     * @return AnswerInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(AnswerInterface $answer): AnswerInterface
    {
        $query = $this->getBuilder()
            ->insert(self::TABLE)
            ->values([
                'id' => ':id',
                'question_id' => ':question_id',
                'user_id' => ':user_id',
                'type' => ':type',
                'status' => ':status',
                'value' => ':value',
            ])
            ->setParameter(':id', $answer->getId()->toString())
            ->setParameter(':question_id', $answer->getQuestionId()->toString())
            ->setParameter(':user_id', $answer->getUserId()->toString())
            ->setParameter(':type', $answer->getType()->getValue())
            ->setParameter(':status', $answer->getStatus()->getValue())
            ->setParameter(':value', $answer->getValue()->toString())
            ->execute();
        if ($query) {
            return $this->getById($answer->getId());
        }

        throw new Exception('Can`t save Answer', 500);
    }

    /**
     * @param AnswerInterface $answer
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function update(AnswerInterface $answer): AnswerInterface
    {
        $query = $this->getBuilder()
            ->update(self::TABLE)
            ->set('question_id', ':question_id')
            ->set('user_id', ':user_id')
            ->set('type', ':type')
            ->set('status', ':status')
            ->set('value', ':value')
            ->where('id = :id')
            ->setParameter(':id', $answer->getId()->toString())
            ->setParameter(':question_id', $answer->getQuestionId()->toString())
            ->setParameter(':user_id', $answer->getUserId()->toString())
            ->setParameter(':type', $answer->getType()->getValue())
            ->setParameter(':status', $answer->getStatus()->getValue())
            ->setParameter(':value', $answer->getValue()->toString())
            ->execute();

        if ($query) {
            return $answer;
        }

        throw new Exception('Can`t update Answer', 500);
    }

    /**
     * @param AnswerId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(AnswerId $id): bool
    {
        $query = $this->getBuilder()
            ->delete(self::TABLE)
            ->where('id = :id')
            ->setParameter(':id', $id->toString())
            ->execute();

        return (bool) $query;
    }
}
