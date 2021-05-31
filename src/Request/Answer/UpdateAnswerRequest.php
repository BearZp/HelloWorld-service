<?php

namespace App\Request\Answer;

use App\DataType\AnswerId;
use App\DataType\AnswerValue;
use App\DataType\QuestionId;
use App\Model\Answer\AnswerTypeEnum;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class UpdateAnswerRequest extends AbstractRequest implements RequestInterface
{
    /** @var AnswerId */
    protected $id;

    /** @var QuestionId */
    protected $questionId;

    /** @var AnswerTypeEnum */
    protected $type;

    /** @var AnswerValue */
    protected $value;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $id = $this->getFromRequest(
            'id',
            AnswerId::class
        );
        $questionId = $this->getFromRequest(
            'question_id',
            QuestionId::class
        );
        $type = $this->getFromRequest(
            'type',
            AnswerTypeEnum::class,
            null
        );
        $value = $this->getFromRequest(
            'value',
            AnswerValue::class
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->id = $id;
        $this->questionId = $questionId;
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return AnswerId
     */
    public function getId(): AnswerId
    {
        return $this->id;
    }

    /**
     * @return QuestionId
     */
    public function getQuestionId(): QuestionId
    {
        return $this->questionId;
    }

    /**
     * @return AnswerTypeEnum
     */
    public function getType(): ?AnswerTypeEnum
    {
        return $this->type;
    }

    /**
     * @return AnswerValue
     */
    public function getValue(): AnswerValue
    {
        return $this->value;
    }
}
