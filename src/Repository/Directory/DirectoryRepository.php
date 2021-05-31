<?php

namespace App\Repository\Directory;

use App\DataType\DirectoryId;
use App\DataType\DirectoryName;
use App\doctrine\pgsql\Connection;
use App\Model\Directory\DirectoryCollection;
use App\Model\Directory\DirectoryFuture;
use App\Model\Directory\DirectoryInterface;
use App\Model\Directory\DirectoryMapper;
use App\Repository\AbstractRepository;
use App\Request\Directory\CreateDirectoryRequest;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Exception;

class DirectoryRepository extends AbstractRepository implements DirectoryRepositoryInterface
{
    protected const TABLE = 'kyc_directory';

    /**
     * CountryRepository constructor.
     * @param Connection $connection
     * @param DirectoryMapper $mapper
     */
    public function __construct(Connection $connection, DirectoryMapper $mapper)
    {
        parent::__construct($connection, $mapper);
    }

    /**
     * @return DirectoryCollection
     * @throws DoctrineDBALException
     */
    public function getAllDirectories(): DirectoryCollection
    {
        /** @var DirectoryCollection  $result */
        $result = parent::getAll(DirectoryCollection::class);
        return $result;
    }

    /**
     * @param DirectoryId $id
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     */
    public function getById(DirectoryId $id): DirectoryInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('id = :id')
            ->setParameter(':id', $id->toString())
            ->execute();

        return new DirectoryFuture(function () use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @param DirectoryName $directoryName
     * @return DirectoryCollection
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getDirectoryByName(DirectoryName $directoryName): DirectoryCollection
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('directory_name = :directory_name')
            ->orderBy('sort_order', 'ASC')
            ->setParameter(':directory_name', $directoryName->toString())
            ->execute();

        return new DirectoryCollection(function () use ($query) {
            $array = [];
            foreach ($query->fetchAllAssociative() as $item) {
                $array[] = $this->mapper->fromArray($item);
            }
            return $array;
        });
    }

    /**
     * @param CreateDirectoryRequest $createDirectoryRequest
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     */
    public function create(CreateDirectoryRequest $createDirectoryRequest): DirectoryInterface
    {
        $sql = 'INSERT INTO ' . self::TABLE . ' (
            directory_name,
            directory_value,
            sort_order,
            status
        ) VALUES (
            :directory_name,
            :directory_value,
            :sort_order,
            :status
        ) RETURNING id';

        $stmt = $this->connection->executeQuery(
            $sql,
            [
                ':directory_name' => $createDirectoryRequest->getDirectoryName()->toString(),
                ':directory_value' => $createDirectoryRequest->getDirectoryValue()->toString(),
                ':sort_order' => $createDirectoryRequest->getSortOrder()->toInteger(),
                ':status' => $createDirectoryRequest->getStatus()->getValue(),
            ]
        );

        $id = $stmt->fetchOne();

        return $this->getById(new DirectoryId($id));
    }

    /**
     * @param DirectoryInterface $directory
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     */
    public function update(DirectoryInterface $directory): DirectoryInterface
    {
        $query = $this->getBuilder()
            ->update(self::TABLE)
            ->set('directory_name', ':directory_name')
            ->set('directory_value', ':directory_value')
            ->set('sort_order', ':sort_order')
            ->set('status', ':status')
            ->where('id = :id')
            ->setParameter(':id', $directory->getId()->toString())
            ->setParameter(':directory_name', $directory->getDirectoryName()->toString())
            ->setParameter(':directory_value', $directory->getDirectoryValue()->toString())
            ->setParameter(':sort_order', $directory->getSortOrder()->toInteger())
            ->setParameter(':status', $directory->getStatus()->getValue())
            ->execute();

        if ($query) {
            return $directory;
        }

        throw new Exception('Can`t update Directory', 500);
    }

    /**
     * @param DirectoryId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(DirectoryId $id): bool
    {
        $query = $this->getBuilder()
            ->delete(self::TABLE)
            ->where('id = :id')
            ->setParameter(':id', $id->toString())
            ->execute();

        return (bool) $query;
    }
}
