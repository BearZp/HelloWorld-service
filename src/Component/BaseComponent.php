<?php

namespace App\Component;

use Tools\logger\LogReferenceTrait;

class BaseComponent implements BaseComponentInterface
{
    use LogReferenceTrait;

    /** @var string */
    protected $requestId;

    /** @var array */
    protected $scope;

    /**
     * @param string $requestId
     */
    public function setRequestId(string $requestId): void
    {
        $this->requestId = $requestId;
    }

    /**
     * @param array $scope
     */
    public function setScope(array $scope): void
    {
        $this->scope = $scope;
    }
}
