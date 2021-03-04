<?php

namespace App\component;

use App\model\collection\CountryCollection;
use App\Repository\CountryRepository;
use Lib\types\IntegerType;

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

    public function getAllByIdAsync()
    {
        $models = [];
        for($i=1; $i<=5; $i++) {
            $models[] = $this->repository->getByIdAsync(new IntegerType($i));
        }
        return new CountryCollection($models);
    }

    public function getAllById()
    {
        $models = [];
        for($i=6; $i<=10; $i++) {
            $models[] = $this->repository->getById(new IntegerType($i));
        }
        return new CountryCollection($models);
    }
}