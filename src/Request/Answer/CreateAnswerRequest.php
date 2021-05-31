<?php

namespace App\Request\Answer;

use App\DataType\AnswerValue;
use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\DataType\UserId;
use App\Model\Answer\AnswerTypeEnum;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class CreateAnswerRequest extends AbstractRequest implements RequestInterface
{
    /** @var QuestionId */
    protected $questionId;

    /** @var UserId */
    protected $userId;

    /** @var AnswerTypeEnum */
    protected $type;

    /** @var AnswerValue */
    protected $value;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $questionId = $this->getFromRequest(
            'question_id',
            QuestionId::class
        );
        $userId = $this->getFromRequest(
            'user_id',
            UserId::class
        );
        $type = $this->getFromRequest(
            'type',
            AnswerTypeEnum::class
        );
        $value = $this->getFromRequest(
            'value',
            AnswerValue::class
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->questionId = $questionId;
        $this->userId = $userId;
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return QuestionId
     */
    public function getQuestionId(): QuestionId
    {
        return $this->questionId;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return AnswerTypeEnum
     */
    public function getType(): AnswerTypeEnum
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
