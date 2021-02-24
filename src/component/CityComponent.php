<?php

namespace App\component;

use App\ddo\CreateCityRequest;
use App\Entity\City;
use App\model\collection\CityCollection;
use App\Repository\CityRepository;
use App\Repository\Stats\CityStatsRepository;


class CityComponent
{
    /** @var CityRepository */
    private $cityRepository;

    /** @var CityStatsRepository */
    private $statsRepository;

    public function __construct(CityRepository $cityRepository, CityStatsRepository $statsRepository)
    {
        $this->cityRepository = $cityRepository;
        $this->statsRepository = $statsRepository;
    }

    /**
     * @return CityCollection
     */
    public function getCityList (): CityCollection
    {
        $cities = $this->statsRepository->findAll();

        return new CityCollection(function() use ($cities) {
            return $cities;
        });
    }

    /**
     * @param CreateCityRequest $createCityRequest
     * @return City
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function createCity(CreateCityRequest $createCityRequest): City
    {
        $city = new City();
        $city->setName($createCityRequest->getName());
        $city->setPopulation($createCityRequest->getPopulation());

         return $this->cityRepository->create($city);
    }
}