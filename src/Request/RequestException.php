<?php

namespace App\Request;

use Exception;

class RequestException extends Exception
{
    /** @var array */
    protected $errors;

    /**
     * RequestException constructor.
     * @param array $errors
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Invalid request parameters', 400);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
