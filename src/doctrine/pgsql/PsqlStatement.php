<?php

namespace App\doctrine\pgsql;

use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\SQLAnywhere\SQLAnywhereException;
use Doctrine\DBAL\Driver\Statement as StatementInterface;
use Doctrine\DBAL\ParameterType;
use IteratorAggregate;

class PsqlStatement implements IteratorAggregate, StatementInterface, Result
{
    private $sql;

    /** @var resource */
    private $dbConn;

    /** @var resource */
    private $result;

    /** @var string */
    private $statementName;

    /** @var int */
    private $fetchMode = PGSQL_ASSOC;

    /** @var DriverConnection */
    private $connection;

    /** @var array */
    private $boundParams = [];

    /**
     * PsqlStatement constructor.
     * @param $sql
     * @param $dbConn
     * @param string $statementName
     * @param DriverConnection $connection
     */
    public function __construct($sql, $dbConn, string $statementName, DriverConnection $connection)
    {
        $this->sql      = $sql;
        $this->dbConn     = $dbConn;
        $this->statementName = $statementName;
        $this->connection = $connection;
    }

    /**
     * @throws SQLAnywhereException
     */
    public function throwError()
    {
        if ($error = pg_result_error($this->result)) {
            $error = $error . ' Executed SQL: ' . $this->sql;
            $this->free(true);
            throw new SQLAnywhereException($error, pg_result_error_field($this->result, PGSQL_DIAG_SQLSTATE));
        } else {
            $this->free(true);
            throw new \Exception('Undefined sql exception');
        }
    }

    /**
     * @return array|false
     */
    public function fetchNumeric()
    {
        if (!$this->result) {
            throw new \Exception('Result undefined');
        }
        $return = pg_fetch_row($this->result);
        if($return === false) {
            $this->throwError();
        }
        $this->free();
        return $return;
    }

    /**
     * @return array|false
     */
    public function fetchAssociative()
    {
        $this->result = pg_get_result($this->dbConn);

        if (!$this->result) {
            throw new \Exception('Result undefined');
        }
        $return = pg_fetch_assoc($this->result);
        if($return === false) {
            $this->throwError();
        }
        $this->free();

        var_dump('------- get result ------' . microtime(true));
//        var_dump($return);

        return $return;
    }

    /**
     * @return array|false|mixed
     */
    public function fetchOne()
    {
        if (!$this->result) {
            throw new \Exception('Result undefined');
        }
        $return = pg_fetch_row($this->result);
        if($return === false) {
            $this->throwError();
        }
        $this->free();
        return $return[0];
    }

    /**
     * @return array
     */
    public function fetchAllNumeric(): array
    {
        if (!$this->result) {
            throw new \Exception('Result undefined');
        }
        $return = pg_fetch_all($this->result, PGSQL_NUM);
        if($return === false) {
            $this->throwError();
        }
        $this->free();
        return $return;
    }

    /**
     * @return array
     */
    public function fetchAllAssociative(): array
    {
        if (!$this->result) {
            throw new \Exception('Result undefined');
        }
        $return = pg_fetch_all($this->result, PGSQL_ASSOC);
        if($return === false) {
            $this->throwError();
        }
        $this->free();
        return $return;
    }

    /**
     * @return array
     */
    public function fetchFirstColumn(): array
    {
        if (!$this->result) {
            throw new \Exception('Result undefined');
        }
        $return = pg_fetch_all_columns($this->result);
        if($return === false) {
            $this->throwError();
        }
        $this->free();
        return $return;
    }

    /**
     * @param false $close
     */
    public function free($close = false): void
    {
        pg_free_result($this->result);
        $this->result = null;
        $this->boundParams = [];
        $this->connection->free($this->dbConn, $close);
    }

    /**
     * @return bool|void
     */
    public function closeCursor()
    {
        $this->free();
    }

    /**
     * @return int|void
     */
    public function columnCount()
    {
        if (!$this->result) {
            throw new \Exception('Result undefined');
        }
        $return = pg_num_fields($this->result);;
        if($return === false) {
            $this->throwError();
        }
        //$this->free(); ???
        return $return;
    }

    /**
     * Sets the fetch mode.
     *
     * @deprecated Use one of the fetch- or iterate-related methods.
     *
     * @param int $fetchMode
     *
     * @return void
     */
    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
    {
        $this->fetchMode = $fetchMode;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Use fetchNumeric(), fetchAssociative() or fetchOne() instead.
     */
    public function fetch($fetchMode = null, $cursorOrientation = \PDO::FETCH_ORI_NEXT, $cursorOffset = 0)
    {
        if (!$fetchMode) {
            $fetchMode = $this->fetchMode;
        }

        if ($fetchMode === PGSQL_NUM) {
            return $this->fetchNumeric();
        }
        return $this->fetchAssociative();
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Use fetchAllNumeric(), fetchAllAssociative() or fetchFirstColumn() instead.
     */
    public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
    {
        if (!$fetchMode) {
            $fetchMode = $this->fetchMode;
        }

        if ($fetchMode === PGSQL_NUM) {
            return $this->fetchAllNumeric();
        }
        return $this->fetchAllAssociative();
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated Use fetchOne() instead.
     */
    public function fetchColumn($columnIndex = 0)
    {
        return $this->fetchOne();
    }

    /**
     * @param int|string $param
     * @param mixed $value
     * @param int $type
     * @return bool
     */
    public function bindValue($param, $value, $type = ParameterType::STRING)
    {
        $this->boundParams[$param] = $value;
        return true;
    }

    public function bindParam($param, &$variable, $type = ParameterType::STRING, $length = null)
    {
        var_dump('bindParam');
        var_dump($param);
        var_dump($variable);
        var_dump($type);
        var_dump($length);
        var_dump($this->sql);
        return true;
    }

    /**
     * @return bool|int|string|null
     */
    public function errorCode()
    {
        if (is_resource($this->result)) {
            return pg_result_error_field($this->result, PGSQL_DIAG_SQLSTATE);
        }
        return false;
    }

    /**
     * @return mixed[]|string
     */
    public function errorInfo()
    {
        if (is_resource($this->result)) {
            return pg_result_error($this->result);
        }
        return '';
    }

    /**
     * @param null $params
     * @return bool
     */
    public function execute($params = null)
    {
        var_dump('------- send query ------' . microtime(true));

        if ($params === null) {
            ksort($this->boundParams);
            $params = [];
            foreach ($this->boundParams as $param) {
                $params[] = $param;
            }
        }

        //var_dump($params);

        if($params) {
            if($this->statementName !== '') {
                $status =  pg_send_execute($this->dbConn, $this->statementName, $params);
            } else {
                $status = pg_send_query_params($this->dbConn, $this->sql, $params);
            }
        } else {
            $status = pg_send_query($this->dbConn, $this->sql);
        }
        // var_dump('SEND QUERY');
        //var_dump($this->sql);
//        if($status) {
//            $this->result = pg_get_result($this->dbConn);
//        }
        return $status;
    }

    /**
     * @return false|int|mixed
     * @throws SQLAnywhereException
     */
    public function rowCount()
    {
        if (!$this->result) {
            throw new \Exception('Result undefined');
        }
        $return = pg_num_rows($this->result);;
        if($return === false) {
            $this->throwError();
        }
        //$this->free(); ???
        return $return;
    }

    /**
     * @return \Generator
     */
    public function getIterator()
    {
        while (($result = $this->fetch()) !== false) {
            yield $result;
        }
    }
}