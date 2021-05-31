<?php

namespace App\Repository\Question;

use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\Doctrine\Exception\NotFoundException;
use App\doctrine\pgsql\Connection;
use App\Model\Level\LevelCollection;
use App\Model\Level\LevelMapper;
use App\Model\Question\QuestionCollection;
use App\Model\Question\QuestionMapper;
use App\Repository\AbstractRepository;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Exception;

class QuestionLevelRepository extends AbstractRepository implements QuestionLevelRepositoryInterface
{
    protected const QUESTION_TABLE = 'kyc_question';
    protected const LEVEL_TABLE = 'kyc_level';
    protected const QUESTION_LEVEL_TABLE = 'kyc_question_level';

    /** @var QuestionMapper */
    protected $questionMapper;

    /** @var LevelMapper */
    protected $levelMapper;

    /**
     * CountryRepository constructor.
     * @param Connection $connection
     * @param QuestionMapper $questionMapper
     * @param LevelMapper $levelMapper
     */
    public function __construct(Connection $connection, QuestionMapper $questionMapper, LevelMapper $levelMapper)
    {
        parent::__construct($connection, $questionMapper);
        $this->questionMapper = $questionMapper;
        $this->levelMapper = $levelMapper;
    }

    /**
     * @param QuestionId $questionId
     * @return LevelCollection
     * @throws DoctrineDBALException
     */
    public function getLevelsForQuestion(QuestionId $questionId): LevelCollection
    {
        $query = $this->getBuilder()
            ->select('l.*')
            ->from(self::LEVEL_TABLE, 'l')
            ->innerJoin('l', 'kyc_question_level', 'ql', 'l.id = ql.level_id')
            ->where('ql.question_id = :question_id')
            ->setParameter(':question_id', $questionId->toString())
            ->execute();

        return new LevelCollection(function () use ($query) {
            try {
                $items = [];
                foreach ($query->fetchAllAssociative() as $item) {
                    $items[] = $this->levelMapper->fromArray($item);
                }
            } catch (NotFoundException $e) {
                $items = [];
            }
            return $items;
        });
    }

    /**
     * @param LevelId $levelId
     * @return QuestionCollection
     * @throws DoctrineDBALException
     */
    public function getQuestionsForLevel(LevelId $levelId): QuestionCollection
    {
        $query = $this->getBuilder()
            ->select('q.*')
            ->from(self::QUESTION_TABLE, 'q')
            ->innerJoin('q', 'kyc_question_level', 'ql', 'q.id = ql.question_id')
            ->where('ql.level_id = :level_id')
            ->setParameter(':level_id', $levelId->toInteger())
            ->execute();

        return new QuestionCollection(function () use ($query) {
            try {
                $items = [];
                foreach ($query->fetchAllAssociative() as $item) {
                    $items[] = $this->questionMapper->fromArray($item);
                }
            } catch (NotFoundException $e) {
                $items = [];
            }
            return $items;
        });
    }

    /**
     * @param QuestionId $questionId
     * @param LevelId $levelId
     * @return bool
     * @throws DoctrineDBALException
     */
    public function attachQuestionToLevel(QuestionId $questionId, LevelId $levelId): bool
    {
        $query = $this->getBuilder()
            ->insert(self::QUESTION_LEVEL_TABLE)
            ->values([
                'question_id' => ':question_id',
                'level_id' => ':level_id',
            ])
            ->setParameter(':question_id', $questionId->toString())
            ->setParameter(':level_id', $levelId->toInteger())
            ->execute();
        if ($query) {
            return (bool) $query;
        }

        throw new Exception('Can`t save Question-Level relation', 500);
    }

    /**
     * @param QuestionId $questionId
     * @param LevelId $levelId
     * @return bool
     * @throws DoctrineDBALException
     */
    public function detachQuestionFromLevel(QuestionId $questionId, LevelId $levelId): bool
    {
        $query = $this->getBuilder()
            ->delete(self::QUESTION_LEVEL_TABLE)
            ->where('question_id = :question_id')
            ->andWhere('level_id = :level_id')
            ->setParameter(':question_id', $questionId->toString())
            ->setParameter(':level_id', $levelId->toString())
            ->execute();

        if ($query) {
            return (bool) $query;
        }

        throw new Exception('Can`t drop Question-Level relation', 500);
    }
}
