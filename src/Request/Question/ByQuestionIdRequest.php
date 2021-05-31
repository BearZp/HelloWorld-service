<?php

namespace App\Request\Question;

use App\DataType\QuestionId;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ByQuestionIdRequest extends AbstractRequest implements RequestInterface
{
    /** @var QuestionId */
    protected $id;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $id = $this->getFromRequest(
            'id',
            QuestionId::class
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->id = $id;
    }

    /**
     * @return QuestionId
     */
    public function getId(): QuestionId
    {
        return $this->id;
    }
}
