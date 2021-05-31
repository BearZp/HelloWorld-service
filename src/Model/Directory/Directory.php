<?php

namespace App\Model\Directory;

use App\DataType\DirectoryId;
use App\DataType\DirectoryName;
use App\DataType\DirectoryValue;
use App\DataType\SortOrder;
use App\Model\StatusEnum;
use Tools\models\mappers\MapperInterface;

class Directory implements DirectoryInterface
{
    /** @var DirectoryId */
    protected $id;

    /** @var DirectoryName */
    protected $directoryName;

    /** @var DirectoryValue */
    protected $directoryValue;

    /** @var SortOrder */
    protected $sortOrder;

    /** @var StatusEnum */
    protected $status;

    /** @var MapperInterface */
    protected $mapper;

    public function __construct(
        DirectoryId $id,
        DirectoryName $directoryName,
        DirectoryValue $directoryValue,
        SortOrder $sortOrder,
        StatusEnum $status,
        MapperInterface $mapper
    ) {
        $this->id = $id;
        $this->directoryName = $directoryName;
        $this->directoryValue = $directoryValue;
        $this->sortOrder = $sortOrder;
        $this->status = $status;
        $this->mapper = $mapper;
    }

    /**
     * @return DirectoryId
     */
    public function getId(): DirectoryId
    {
        return $this->id;
    }

    /**
     * @return DirectoryName
     */
    public function getDirectoryName(): DirectoryName
    {
        return $this->directoryName;
    }

    /**
     * @return DirectoryValue
     */
    public function getDirectoryValue(): DirectoryValue
    {
        return $this->directoryValue;
    }

    /**
     * @return SortOrder
     */
    public function getSortOrder(): SortOrder
    {
        return $this->sortOrder;
    }

    /**
     * @return StatusEnum
     */
    public function getStatus(): StatusEnum
    {
        return $this->status;
    }

    /**
     * @return MapperInterface
     */
    public function getMapper(): MapperInterface
    {
        return $this->mapper;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->mapper->toArray($this);
    }
}
