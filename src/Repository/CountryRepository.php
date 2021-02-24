<?php

namespace App\Repository;

use App\model\collection\CountryCollection;
use App\model\mapper\CountryMapper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Connection;

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
        parent::__construct($connection, $mapper);
    }

    /**
     * @param Criteria|null $criteria
     * @return CountryCollection
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAll(Criteria $criteria = null): CountryCollection
    {
        $query = $this->queryBuilder
            ->select('*')
            ->from(self::TABLE)
            ->execute();

        $countries = [];
        foreach($query->fetchAllAssociative() as $item) {
            $countries[] = $this->mapper->fromArray($item);
        }
        return new CountryCollection($countries);
    }
}