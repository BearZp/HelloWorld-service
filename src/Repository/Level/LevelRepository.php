<?php

namespace App\Repository\Level;

use App\DataType\LevelId;
use App\doctrine\pgsql\Connection;
use App\Model\Level\LevelCollection;
use App\Model\Level\LevelFuture;
use App\Model\Level\LevelInterface;
use App\Model\Level\LevelMapper;
use App\Repository\AbstractRepository;
use App\Request\Level\CreateLevelRequest;
use App\Request\Level\UpdateLevelRequest;
use Doctrine\Common\Collections\Criteria;
use Tools\types\IntegerType;
use \Doctrine\DBAL\Exception as DoctrineDBALException;
use \Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;

class LevelRepository extends AbstractRepository implements LevelRepositoryInterface
{
    protected const TABLE = 'kyc_level';

    /**
     * CountryRepository constructor.
     * @param Connection $connection
     * @param LevelMapper $mapper
     */
    public function __construct(Connection $connection, LevelMapper $mapper)
    {
        parent::__construct($connection, $mapper);
    }

    /**
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function getStartLevel(): LevelInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('is_first = true')
            ->execute();

        return new LevelFuture(function () use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function getSecondLevel(): LevelInterface
    {
        $sql = 'SELECT * FROM ' . self::TABLE . ' WHERE parent_level_id = 
            (SELECT id FROM ' . self::TABLE . ' WHERE is_first = true limit 1)';

        $query = $this->connection->executeQuery($sql);
        return new LevelFuture(function () use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @param LevelId $id
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function findNextLevel(LevelId $id): LevelInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('parent_level_id = :id')
            ->setParameter(':id', $id->toString())
            ->execute();

        return new LevelFuture(function () use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @param LevelId $id
     * @return LevelInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function findParentLevel(LevelId $id): LevelInterface
    {
        $sql = 'SELECT * FROM ' . self::TABLE . ' WHERE id = 
            (SELECT parent_level_id FROM ' . self::TABLE . ' WHERE id = :id limit 1)';

        $query = $this->connection->executeQuery($sql, [':id' => $id->toInteger()]);
        return new LevelFuture(function () use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @return LevelCollection
     * @throws DoctrineDBALException
     */
    public function getAllLevels(): LevelCollection
    {
        /** @var LevelCollection  $result */
        $result = parent::getAll(LevelCollection::class);
        return $result;
    }

    /**
     * @param LevelId $id
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function getById(LevelId $id): LevelInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter(':id', $id->toString())
            ->execute();

        return new LevelFuture(function () use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @param LevelId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(LevelId $id): bool
    {
        $query = $this->getBuilder()
            ->delete(self::TABLE)
            ->where('id = :id')
            ->setParameter(':id', $id->toString())
            ->execute();

        return (bool) $query;
    }

    /**
     * @param CreateLevelRequest $createLevelRequest
     * @return LevelInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(CreateLevelRequest $createLevelRequest): LevelInterface
    {
        $sql = 'INSERT INTO ' . self::TABLE . ' (
            name,
            description,
            advertising_text,
            parent_level_id,
            is_first
        ) VALUES (
            :name,
            :description,
            :advertisingText,
            :parentLevel,
            :isFirst
        ) RETURNING id';

        $stmt = $this->connection->executeQuery(
            $sql,
            [
                ':name' => $createLevelRequest->getName()->toString(),
                ':description' => $createLevelRequest->getDescription()->toString(),
                ':advertisingText' => $createLevelRequest->getAdvertisingText()->toString(),
                ':parentLevel' => $createLevelRequest->getParentLevel()->toInteger(),
                ':isFirst' => $createLevelRequest->getIsFirst()->toString(),
            ]
        );

        $id = new LevelId($stmt->fetchOne());

        if ($createLevelRequest->getIsFirst()->isTrue()) {
            $query = $this->getBuilder()
                ->update(self::TABLE)
                ->set('is_first', 'false')
                ->where('id <> :id')
                ->setParameter(':id', $id->toString())
                ->execute();
        }

        return $this->getById($id);
    }

    /**
     * @param LevelInterface $level
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function update(LevelInterface $level): LevelInterface
    {
        $this->getBuilder()
            ->update(self::TABLE)
            ->set('name', ':name')
            ->set('description', ':description')
            ->set('advertising_text', ':advertisingText')
            ->set('parent_level_id', ':parentLevel')
            ->set('is_first', ':isFirst')
            ->where('id = :id')
            ->setParameter(':id', $level->getId()->toString())
            ->setParameter(':name', $level->getName()->toString())
            ->setParameter(':description', $level->getDescription()->toString())
            ->setParameter(':advertisingText', $level->getAdvertisingText()->toString())
            ->setParameter(':parentLevel', $level->getParentLevel()->toInteger())
            ->setParameter(':isFirst', $level->getIsFirst()->toString())
            ->execute();

        if ($level->getIsFirst()->isTrue()) {
            $query = $this->getBuilder()
                ->update(self::TABLE)
                ->set('is_first', 'false')
                ->where('id <> :id')
                ->setParameter(':id', $level->getId()->toString())
                ->execute();
        }

        return $this->getById($level->getId());
    }
}
