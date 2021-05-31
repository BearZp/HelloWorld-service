<?php

namespace App\Repository;

use Tools\types\UuidType;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Doctrine\DBAL\Exception as DoctrineDBALException;

interface RepositoryInterface
{
    /**
     * @return UuidType
     * @throws DoctrineDBALDriverException
     * @throws DoctrineDBALException
     */
    public function getNewUuidId(): UuidType;
}
