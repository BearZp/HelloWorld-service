<?php

namespace App\Repository;

use App\doctrine\pgsql\Connection;
use App\model\collection\BookCollection;
use App\model\BookFuture;
use App\model\BookInterface;
use App\model\mapper\BookMapper;
use Doctrine\Common\Collections\Criteria;
use Lib\types\IntegerType;

class BookRepository extends AbstractRepository
{
    private const TABLE = 'books';

    /**
     * CountryRepository constructor.
     * @param Connection $connection
     * @param BookMapper $mapper
     */
    public function __construct(Connection $connection, BookMapper $mapper)
    {
        parent::__construct($connection, $mapper);
    }

    /**
     * @param Criteria|null $criteria
     * @return BookCollection
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(Criteria $criteria = null): BookCollection
    {
        $query = $this->getBuilder()
            ->select('*, sleep')
            ->from(self::TABLE)
            ->execute();

        return new BookCollection(function() use ($query) {
            $countries = [];
            foreach($query->fetchAllAssociative() as $item) {
                $countries[] = $this->mapper->fromArray($item);
            }
            return $countries;
        });
    }

    /**
     * @param IntegerType $id
     * @return CountryInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function getByIdAsync(IntegerType $id): BookInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE . ', pg_sleep(1)')
            ->where('id = ' . $id->toString())
            ->execute();

        return new BookFuture(function() use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @param IntegerType $id
     * @return CountryInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function getById(IntegerType $id): CountryInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE . ', pg_sleep(1)')
            ->where('id = ' . $id->toString())
            ->execute();

        return $this->mapper->fromArray($query->fetchAssociative());
    }
}