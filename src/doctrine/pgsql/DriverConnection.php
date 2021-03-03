<?php

namespace App\doctrine\pgsql;

use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Query\QueryBuilder;

class DriverConnection implements \Doctrine\DBAL\Driver\Connection
{
    /** @var ConnectionPool */
    private $connectionPool;

    /**
     * @var resource
     */
    private $defaultConnection;

    /**
     * @var bool
     */
    private $isTransaction = false;

    /**
     * Connection constructor.
     * @param string $connectionString
     * @param int|null $maxConnections
     * @throws Exception
     */
    public function __construct(string $connectionString, int $maxConnections)
    {
        $this->connectionPool = new ConnectionPool($connectionString, $maxConnections);
        $this->defaultConnection = $this->connectionPool->getConnection(true);
    }

    /**
     * @param string $sql
     * @return PsqlStatement
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function prepare($sql): PsqlStatement
    {
        $dbConnection = $this->connectionPool->getConnection($this->isTransaction);
        $statementName = '';
        if(strpos('$1', $sql)) {
            $statementName = crc32($sql);
            //prepare sql
            pg_send_prepare($dbConnection, $statementName, $sql);
            //clean connection
            ($res = pg_get_result($dbConnection)) ? pg_fetch_all($res) : false;
        }
        return new PsqlStatement($sql, $dbConnection, $statementName, $this);
    }

    /**
     * @return PsqlStatement|Statement
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function query()
    {
        $args = func_get_args();
        $stmt = $this->prepare($args[0]);

        $stmt->execute();

        return $stmt;
    }

    /**
     * @param mixed $value
     * @param int $type
     * @return int|mixed|string
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function quote($value, $type = ParameterType::STRING)
    {
        $result = '';
        $connection = $this->connectionPool->getConnection();
        switch ($type) {
            case ParameterType::LARGE_OBJECT :
            case ParameterType::ASCII :
            case ParameterType::STRING : {
                $result = pg_escape_literal($connection, $value);
            }
            break;
            case ParameterType::INTEGER : {
                $result = (int) $value;
            }
            break;
            case ParameterType::BINARY : {
                $result = pg_escape_bytea($connection, $value);
            }
            break;
            case ParameterType::BOOLEAN : {
                $result = $value ? 'TRUE' : 'FALSE';
            }
            break;
            case ParameterType::NULL : {
                $result = 'NULL';
            }
            break;
        }
        return $result;
    }

    /**
     * @param string $sql
     * @return int
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function exec($sql)
    {
        $connection = $this->connectionPool->getConnection($this->isTransaction);
        $result = pg_query($connection, $sql);
        return pg_affected_rows($result);
    }

    public function lastInsertId($name = null)
    {
        throw new \Exception('Method not implemented');
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function beginTransaction()
    {
        $this->isTransaction = true;
        $connection = $this->connectionPool->getConnection($this->isTransaction);
        if (!pg_query($connection, 'BEGIN')) {
            throw new \Exception('Could not start transaction');
        }

        return true;
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function commit()
    {
        $connection = $this->connectionPool->getConnection($this->isTransaction);
        $this->isTransaction = false;
        if (!pg_query($connection, 'COMMIT')) {
            throw new \Exception('Transaction commit failed');
        }
        return true;
    }

    /**
     * @return bool
     * @throws Exception
     * @throws \Doctrine\DBAL\ConnectionException
     */
    public function rollBack()
    {
        $connection = $this->connectionPool->getConnection($this->isTransaction);
        $this->isTransaction = false;
        if (!pg_query($connection, 'ROLLBACK')) {
            throw new \Exception('Transaction rollback failed');
        }

        return true;
    }

    public function errorCode()
    {
        // TODO: Implement errorCode() method.
    }

    public function errorInfo()
    {
        // TODO: Implement errorInfo() method.
    }

    /**
     * @param $connection
     * @param false $close
     * @return bool
     */
    public function free($connection, $close = false)
    {
        return $this->connectionPool->freeConnection($connection, $close);
    }

//    /**
//     * Creates a new instance of a SQL query builder.
//     *
//     * @return QueryBuilder
//     */
//    public function createQueryBuilder()
//    {
//        return new QueryBuilder($this);
//    }
}