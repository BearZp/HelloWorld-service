<?php

namespace App\Request\Answer;

use App\DataType\AnswerId;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ByAnswerIdRequest extends AbstractRequest implements RequestInterface
{
    /** @var AnswerId */
    protected $id;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $id = $this->getFromRequest(
            'id',
            AnswerId::class
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->id = $id;
    }

    /**
     * @return AnswerId
     */
    public function getId(): AnswerId
    {
        return $this->id;
    }
}
