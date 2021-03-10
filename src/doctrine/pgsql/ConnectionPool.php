<?php

namespace App\doctrine\pgsql;

use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\Exception;

class ConnectionPool
{
    /**
     * @var array
     */
    private $connectionPool = [];

    /**
     * @var array
     */
    private $poolMap = [];

    /**
     * @var int
     */
    private $maxConnection;

    /**
     * @var string
     */
    private $connectionString;

    /**
     * @var resource
     */
    private $defaultConnection;

    private const CONNECTION_FREE = 1,
        CONNECTION_BUSY = 2;

    /**
     * Connection constructor.
     * @param string $connectionString
     * @param int|null $maxConnections
     * @throws Exception
     */
    public function __construct(string $connectionString, int $maxConnections)
    {
        $this->maxConnection = $maxConnections;
        $this->connectionString = $connectionString;
        $this->defaultConnection = $this->createNewConnection();
    }

    /**
     * @return resource
     * @throws Exception
     */
    private function createNewConnection()
    {
        $connection = pg_connect($this->connectionString, PGSQL_CONNECT_FORCE_NEW);
        if ($connection === false) {
            throw new Exception('pg_pconnect: could not connect to server: ');
        }
        return $connection;
    }

    /**
     * @param false $getDefault
     * @return mixed
     * @throws ConnectionException
     * @throws Exception
     */
    public function getConnection($getDefault = false)
    {
        if ($getDefault) {
            return $this->defaultConnection;
        }

//        var_dump('+++++++++++++++++++++++++++++++++++++++++++');
//        var_dump("pool size => " . count($this->connectionPool));

        foreach ($this->poolMap as $item => $value) {
            if ($value === self::CONNECTION_FREE && !pg_connection_busy($this->connectionPool[$item])) {
                return $this->connectionPool[$item];
            }
        }

        //try to create new connection if poll is not full
        if(count($this->connectionPool) < $this->maxConnection) {
            $connection = $this->createNewConnection();
            $connectionNumber = get_resource_id($connection);
            $this->connectionPool[$connectionNumber] = $connection;
            $this->poolMap[$connectionNumber] = self::CONNECTION_BUSY;
            // var_dump('Create new connection => ' . $connectionNumber);
            return $connection;
        }

        throw new ConnectionException('Max connection reached');
    }

    /**
     * @param $conn
     * @param false $close
     * @return bool
     */
    public function freeConnection($conn, $close = false): bool
    {
        if($close) {
            $connectionNumber = get_resource_id($conn);
            unset ($this->poolMap[$connectionNumber]);
            unset ($this->connectionPool[$connectionNumber]);
            pg_close($conn);
        } else {
            $this->poolMap[get_resource_id($conn)] = self::CONNECTION_FREE;
        }

        return true;
    }
}