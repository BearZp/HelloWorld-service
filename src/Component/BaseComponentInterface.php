<?php

namespace App\Component;

interface BaseComponentInterface
{
    /**
     * @param string $requestId
     */
    public function setRequestId(string $requestId): void;

    /**
     * @param array $scope
     */
    public function setScope(array $scope): void;
}
