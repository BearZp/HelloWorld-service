<?php

namespace App\DataFixtures;

use App\Entity\City;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CityFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $city1 = new City();
        $city1->setName("Bratislava");
        $city1->setPopulation(432000);
        $manager->persist($city1);

        $city2 = new City();
        $city2->setName("Budapest");
        $city2->setPopulation(1759000);
        $manager->persist($city2);

        $city3 = new City();
        $city3->setName("Prague");
        $city3->setPopulation(1280000);
        $manager->persist($city3);

        $city4 = new City();
        $city4->setName("Warsaw");
        $city4->setPopulation(1748000);
        $manager->persist($city4);

        $city5 = new City();
        $city5->setName("Los Angeles");
        $city5->setPopulation(3971000);
        $manager->persist($city5);

        $city6 = new City();
        $city6->setName("New York");
        $city6->setPopulation(8550000);
        $manager->persist($city6);

        $city7 = new City();
        $city7->setName("Edinburgh");
        $city7->setPopulation(464000);
        $manager->persist($city7);

        $city8 = new City();
        $city8->setName("Berlin");
        $city8->setPopulation(3671000);
        $manager->persist($city8);

        $manager->flush();
    }
}
