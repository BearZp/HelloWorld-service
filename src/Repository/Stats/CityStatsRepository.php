<?php

namespace App\Repository\Stats;

use App\Entity\City;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;


/**
 * @method City|null find($id, $lockMode = null, $lockVersion = null)
 * @method City|null findOneBy(array $criteria, array $orderBy = null)
 * @method City[]    findAll()
 * @method City[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CityStatsRepository extends EntityRepository
{
    /**
     * CityStatsRepository constructor.
     * Initializes a new <tt>EntityRepository</tt>.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $class = new ClassMetadata(City::class);
        parent::__construct($em, $class);
    }
}