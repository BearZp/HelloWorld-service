<?php

namespace App\Model\Directory;

use App\DataType\DirectoryId;
use App\DataType\DirectoryName;
use App\DataType\DirectoryValue;
use App\DataType\SortOrder;
use App\Model\StatusEnum;
use Tools\models\FutureObjectInterface;
use Tools\models\FutureObjectTrait;
use Tools\models\mappers\MapperInterface;
use Throwable;

class DirectoryFuture implements DirectoryInterface, FutureObjectInterface
{
    use FutureObjectTrait {
        FutureObjectTrait::get as getModel;
    }

    /**
     * @return DirectoryId
     * @throws Throwable
     */
    public function getId(): DirectoryId
    {
        return $this->getModel()->getId();
    }

    /**
     * @return DirectoryName
     * @throws Throwable
     */
    public function getDirectoryName(): DirectoryName
    {
        return $this->getModel()->getDirectoryName();
    }

    /**
     * @return DirectoryValue
     * @throws Throwable
     */
    public function getDirectoryValue(): DirectoryValue
    {
        return $this->getModel()->getDirectoryValue();
    }

    /**
     * @return SortOrder
     * @throws Throwable
     */
    public function getSortOrder(): SortOrder
    {
        return $this->getModel()->getSortOrder();
    }

    /**
     * @return StatusEnum
     * @throws Throwable
     */
    public function getStatus(): StatusEnum
    {
        return $this->getModel()->getStatus();
    }

    /**
     * @return MapperInterface
     * @throws Throwable
     */
    public function getMapper(): MapperInterface
    {
        return $this->getModel()->getMapper();
    }

    /**
     * @return array
     * @throws Throwable
     */
    public function toArray(): array
    {
        return $this->getModel()->toArray();
    }
}
