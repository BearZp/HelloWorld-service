<?php

namespace App\Repository;

use App\doctrine\pgsql\Connection;
use App\model\collection\CountryCollection;
use App\model\CountryFuture;
use App\model\CountryInterface;
use App\model\mapper\CountryMapper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Lib\types\IntegerType;

class CountryRepository extends AbstractRepository
{
    private const TABLE = 'countries';

    /**
     * CountryRepository constructor.
     * @param Connection $connection
     * @param CountryMapper $mapper
     */
    public function __construct(Connection $connection, CountryMapper $mapper)
    {
        //var_dump('Driver class in repository >>>  ' . get_class($connection->getDriver()));
        parent::__construct($connection, $mapper);
    }

    /**
     * @param Criteria|null $criteria
     * @return CountryCollection
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(Criteria $criteria = null): CountryCollection
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->execute();

        return new CountryCollection(function() use ($query) {
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
    public function getByIdAsync(IntegerType $id): CountryInterface
    {
        $query = $this->getBuilder()
            ->select('c1.*')
            ->from(self::TABLE, 'c1')
            ->groupBy('c1.id')
            ->having('c1.id = :id')
            ->join('c1', self::TABLE, 'c2', 'c2.id = c1.id')
            ->join('c1', self::TABLE, 'c3', 'c3.id = c1.id')
            ->join('c1', self::TABLE, 'c4', 'c4.id = c1.id')
            ->setParameter(':id',$id->toString())
            ->execute();

        return new CountryFuture(function() use ($query) {
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
            ->select('c1.*')
            ->from(self::TABLE, 'c1')
            ->groupBy('c1.id')
            ->having('c1.id = ' . $id->toString())
            ->join('c1', self::TABLE, 'c2', 'c2.id = c1.id')
            ->join('c1', self::TABLE, 'c3', 'c3.id = c1.id')
            ->join('c1', self::TABLE, 'c4', 'c4.id = c1.id')
            ->execute();

        return $this->mapper->fromArray($query->fetchAssociative());
    }
}