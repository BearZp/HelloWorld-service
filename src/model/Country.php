<?php

namespace App\model;

use Lib\models\mappers\MapperInterface;
use Lib\types\IntegerType;
use Lib\types\StringType255;

class Country implements CountryInterface
{
    /** @var IntegerType */
    private $id;

    /** @var StringType255 */
    private $name;

    /** @var IntegerType */
    private $population;

    /** @var MapperInterface */
    private $mapper;

    /**
     * Country constructor.
     * @param IntegerType $id
     * @param StringType255 $name
     * @param IntegerType $population
     * @param MapperInterface $mapper
     */
    public function __construct(IntegerType $id, StringType255 $name, IntegerType $population, MapperInterface $mapper)
    {
        $this->id = $id;
        $this->name = $name;
        $this->population = $population;
        $this->mapper = $mapper;
    }

    /**
     * @return IntegerType
     */
    public function getId(): IntegerType
    {
        return $this->id;
    }

    /**
     * @return StringType255
     */
    public function getName(): StringType255
    {
        return $this->name;
    }

    /**
     * @return IntegerType
     */
    public function getPopulation(): IntegerType
    {
        return $this->population;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->mapper->toArray($this);
    }
}