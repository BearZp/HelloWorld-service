<?php

namespace App\model;

use Lib\models\FutureObjectInterface;
use Lib\models\FutureObjectTrait;
use Lib\types\IntegerType;
use Lib\types\StringType255;

class CountryFuture implements CountryInterface, FutureObjectInterface
{

    use FutureObjectTrait {
        FutureObjectTrait::get as getModel;
    }

    public function getId(): IntegerType
    {
        return $this->getModel()->getId();
    }

    public function getName(): StringType255
    {
        return $this->getModel()->getName();
    }

    public function getPopulation(): IntegerType
    {
        return $this->getModel()->getPopulation();
    }

    public function toArray()
    {
        return $this->getModel()->toArray();
    }
}