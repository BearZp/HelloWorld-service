<?php

namespace App\Repository;

use Doctrine\DBAL\Connection;
use Lib\models\mappers\MapperInterface;
use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractRepository
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
     * @return QueryBuilder
     */
    public function getBuilder(): QueryBuilder
    {
        return $this->connection->createQueryBuilder();
    }
}