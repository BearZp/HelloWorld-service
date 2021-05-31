<?php

namespace App\Repository\Directory;

use App\DataType\DirectoryId;
use App\DataType\DirectoryName;
use App\Model\Directory\DirectoryCollection;
use App\Model\Directory\DirectoryInterface;
use App\Repository\RepositoryInterface;
use App\Request\Directory\CreateDirectoryRequest;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Doctrine\DBAL\Exception as DoctrineDBALException;

interface DirectoryRepositoryInterface extends RepositoryInterface
{
    /**
     * @param DirectoryId $id
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     */
    public function getById(DirectoryId $id): DirectoryInterface;

    /**
     * @param DirectoryName $directoryName
     * @return DirectoryCollection
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getDirectoryByName(DirectoryName $directoryName): DirectoryCollection;

    /**
     * @return DirectoryCollection
     * @throws DoctrineDBALException
     */
    public function getAllDirectories(): DirectoryCollection;

    /**
     * @param CreateDirectoryRequest $createDirectoryRequest
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(CreateDirectoryRequest $createDirectoryRequest): DirectoryInterface;

    /**
     * @param DirectoryInterface $directory
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     */
    public function update(DirectoryInterface $directory): DirectoryInterface;

    /**
     * @param DirectoryId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(DirectoryId $id): bool;
}
