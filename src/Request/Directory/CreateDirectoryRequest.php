<?php

namespace App\Request\Directory;

use App\DataType\DirectoryName;
use App\DataType\DirectoryValue;
use App\DataType\SortOrder;
use App\Model\StatusEnum;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class CreateDirectoryRequest extends AbstractRequest implements RequestInterface
{
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

        $directoryName = $this->getFromRequest(
            'directory_name',
            DirectoryName::class
        );
        $directoryValue = $this->getFromRequest(
            'directory_value',
            DirectoryValue::class
        );
        $sortOrder = $this->getFromRequest(
            'sort_order',
            SortOrder::class,
            new SortOrder(0)
        );
        $status = $this->getFromRequest(
            'status',
            StatusEnum::class,
            new StatusEnum(StatusEnum::ACTIVE)
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->directoryName = $directoryName;
        $this->directoryValue = $directoryValue;
        $this->sortOrder = $sortOrder;
        $this->status = $status;
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
