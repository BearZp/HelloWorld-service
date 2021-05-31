<?php

namespace App\Model\Directory;

use App\DataType\DirectoryId;
use App\DataType\DirectoryName;
use App\DataType\DirectoryValue;
use App\DataType\SortOrder;
use App\Model\StatusEnum;
use Tools\models\ModelInterface;

interface DirectoryInterface extends ModelInterface
{
    /**
     * @return DirectoryId
     */
    public function getId(): DirectoryId;

    /**
     * @return DirectoryName
     */
    public function getDirectoryName(): DirectoryName;

    /**
     * @return DirectoryValue
     */
    public function getDirectoryValue(): DirectoryValue;

    /**
     * @return SortOrder
     */
    public function getSortOrder(): SortOrder;

    /**
     * @return StatusEnum
     */
    public function getStatus(): StatusEnum;
}
