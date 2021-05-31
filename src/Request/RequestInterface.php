<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\Request;

interface RequestInterface
{
    /**
     * CreateLevelRequest constructor.
     * @param Request $request
     * @throws RequestException
     */
    public function __construct(Request $request);
}
