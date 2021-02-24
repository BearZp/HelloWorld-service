<?php

namespace App\doctrine\pgsql;

use Doctrine\DBAL\Driver\AbstractPostgreSQLDriver;

class Driver extends AbstractPostgreSQLDriver
{

    public function connect(array $params, $username = null, $password = null, array $driverOptions = []): \Doctrine\DBAL\Driver\Connection
    {
        try {
            return new Connection($params, (string) $username, (string) $password, $driverOptions);
        } catch (MysqliException $e) {
            throw Exception::driverException($this, $e);
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return 'PostgreSQL';
    }
}