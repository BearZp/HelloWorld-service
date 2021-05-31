<?php

namespace App\Request\Directory;

use App\DataType\DirectoryId;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ByDirectoryIdRequest extends AbstractRequest implements RequestInterface
{
    /** @var DirectoryId */
    protected $id;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $id = $this->getFromRequest(
            'id',
            DirectoryId::class
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->id = $id;
    }

    /**
     * @return DirectoryId
     */
    public function getId(): DirectoryId
    {
        return $this->id;
    }
}
