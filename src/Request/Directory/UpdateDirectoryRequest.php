<?php

namespace App\Request\Directory;

use App\DataType\DirectoryId;
use App\DataType\DirectoryName;
use App\DataType\DirectoryValue;
use App\DataType\SortOrder;
use App\Model\StatusEnum;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Tools\types\IntegerType;
use Tools\types\StringType255;

class UpdateDirectoryRequest extends AbstractRequest implements RequestInterface
{
    /** @var DirectoryId */
    protected $id;

    /** @var DirectoryName  */
    protected $directoryName;

    /** @var DirectoryValue */
    protected $directoryValue;

    /** @var SortOrder */
    protected $sortOrder;

    /** @var StatusEnum */
    protected $status;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $id = $this->getFromRequest(
            'id',
            DirectoryId::class
        );
        $directoryName = $this->getFromRequest(
            'directory_name',
            DirectoryName::class,
            null
        );
        $directoryValue = $this->getFromRequest(
            'directory_value',
            DirectoryValue::class,
            null
        );
        $sortOrder = $this->getFromRequest(
            'sort_order',
            SortOrder::class,
            null
        );
        $status = $this->getFromRequest(
            'status',
            StatusEnum::class,
            null
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->id = $id;
        $this->directoryName = $directoryName;
        $this->directoryValue = $directoryValue;
        $this->sortOrder = $sortOrder;
        $this->status = $status;
    }

    /**
     * @return DirectoryId
     */
    public function getId(): ?DirectoryId
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
    public function getSortOrder(): ?SortOrder
    {
        return $this->sortOrder;
    }

    /**
     * @return StatusEnum
     */
    public function getStatus(): ?StatusEnum
    {
        return $this->status;
    }
}
