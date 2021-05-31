<?php

namespace App\Request\Directory;

use App\DataType\DirectoryName;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ByDirectoryNameRequest extends AbstractRequest implements RequestInterface
{
    /** @var DirectoryName */
    protected $name;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $name = $this->getFromRequest(
            'directory_name',
            DirectoryName::class
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->name = $name;
    }

    /**
     * @return DirectoryName
     */
    public function getName(): DirectoryName
    {
        return $this->name;
    }
}
