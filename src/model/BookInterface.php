<?php

namespace App\model;

use Lib\types\IntegerType;
use Lib\types\StringType255;

interface BookInterface
{
    /**
     * @return IntegerType
     */
    public function getId(): IntegerType;

    /**
     * @return StringType255
     */
    public function getName(): StringType255;

    /**
     * @return IntegerType
     */
    public function getPages(): IntegerType;

    /**
     * @return array
     */
    public function toArray(): array;
}