<?php

namespace App\doctrine\pgsql;

use Doctrine\DBAL\Driver\Statement;
use Doctrine\DBAL\ParameterType;

class Connection implements \Doctrine\DBAL\Driver\Connection
{

    public function prepare($sql)
    {
        // TODO: Implement prepare() method.
    }

    public function query()
    {
        // TODO: Implement query() method.
    }

    public function quote($value, $type = ParameterType::STRING)
    {
        // TODO: Implement quote() method.
    }

    public function exec($sql)
    {
        // TODO: Implement exec() method.
    }

    public function lastInsertId($name = null)
    {
        // TODO: Implement lastInsertId() method.
    }

    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    public function commit()
    {
        // TODO: Implement commit() method.
    }

    public function rollBack()
    {
        // TODO: Implement rollBack() method.
    }

    public function errorCode()
    {
        // TODO: Implement errorCode() method.
    }

    public function errorInfo()
    {
        // TODO: Implement errorInfo() method.
    }
}