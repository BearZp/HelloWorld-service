<?php

namespace App\Repository;

use App\Model\Level\LevelCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\ResultStatement;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Tools\collection\AbstractImmutableCollection;
use Tools\models\mappers\MapperInterface;
use Doctrine\DBAL\Query\QueryBuilder;
use Tools\types\UuidType;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * EsppTransactionRepository constructor.
     *
     * @param Connection      $connection
     * @param MapperInterface $mapper
     */
    public function __construct(Connection $connection, MapperInterface $mapper)
    {
        $this->connection = $connection;
        $this->mapper = $mapper;
    }

    /**
     * @param string $collectionClass
     * @return AbstractImmutableCollection
     * @throws DoctrineDBALException
     */
    public function getAll(string $collectionClass): AbstractImmutableCollection
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(static::TABLE)
            ->execute();

        return $this->buildCollection($collectionClass, $query);
    }

    /**
     * @return QueryBuilder
     */
    protected function getBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }

    /**
     * @param string $collectionClass
     * @param ResultStatement $statement
     * @return mixed
     */
    protected function buildCollection(string $collectionClass, ResultStatement $statement): AbstractImmutableCollection
    {
        return new $collectionClass(function () use ($statement) {
            $items = [];
            foreach ($statement->fetchAllAssociative() as $item) {
                $items[] = $this->mapper->fromArray($item);
            }
            return $items;
        });
    }

    /**
     * @return UuidType
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNewUuidId(): UuidType
    {
        $uuid = $this->connection->executeQuery('select uuid_generate_v4()')->fetchOne();
        return new UuidType(str_replace('-', '', $uuid));
    }
}
