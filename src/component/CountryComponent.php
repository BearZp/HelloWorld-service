<?php

namespace App\component;

use App\model\Country;
use App\Repository\CountryRepository;

class CountryComponent
{
    /**
     * @var CountryRepository
     */
    private $repository;

    public function __construct(CountryRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getAll()
    {
        return $this->repository->getAll();
    }
}