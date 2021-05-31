<?php

namespace App\Request\Question;

use App\DataType\DirectoryName;
use App\DataType\LevelId;
use App\DataType\QuestionDescription;
use App\DataType\QuestionTitle;
use App\DataType\SortOrder;
use App\Model\Level\LevelIdCollection;
use App\Model\StatusEnum;
use App\Model\Question\QuestionTypeEnum;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class CreateQuestionRequest extends AbstractRequest implements RequestInterface
{
    /** @var LevelIdCollection */
    protected $levels;

    /** @var QuestionTitle  */
    protected $questionTitle;

    /** @var QuestionDescription */
    protected $questionDescription;

    /** @var SortOrder */
    protected $sortOrder;

    /** @var QuestionTypeEnum */
    protected $type;

    /** @var StatusEnum */
    protected $status;

    /** @var DirectoryName|null */
    protected $directoryName;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $levels = $this->getCollectionFromRequest(
            'levels',
            LevelIdCollection::class,
            LevelId::class
        );
        $questionTitle = $this->getFromRequest(
            'question_title',
            QuestionTitle::class
        );
        $questionDescription = $this->getFromRequest(
            'question_description',
            QuestionDescription::class
        );
        $sortOrder = $this->getFromRequest(
            'sort_order',
            SortOrder::class,
            new SortOrder(0)
        );
        $type = $this->getFromRequest(
            'type',
            QuestionTypeEnum::class
        );
        $status = $this->getFromRequest(
            'status',
            StatusEnum::class,
            new StatusEnum(StatusEnum::ACTIVE)
        );
        $directoryName = $this->getFromRequest(
            'directory_name',
            DirectoryName::class,
            null
        );

        $selectType = new QuestionTypeEnum(QuestionTypeEnum::SELECT);
        if ($selectType->isEqual($type) && $directoryName === null) {
            $this->errors[] = 'Directory name is mandatory for question type ' . $selectType->getKey();
        }

        if ($this->errors) {
            throw new RequestException($this->errors);
        }


        $this->levels = $levels;
        $this->questionTitle = $questionTitle;
        $this->questionDescription = $questionDescription;
        $this->sortOrder = $sortOrder;
        $this->type = $type;
        $this->status = $status;
        $this->directoryName = $directoryName;
    }

    /**
     * @return LevelIdCollection
     */
    public function getLevels(): ?LevelIdCollection
    {
        return $this->levels;
    }

    /**
     * @return QuestionTitle
     */
    public function getQuestionTitle(): ?QuestionTitle
    {
        return $this->questionTitle;
    }

    /**
     * @return QuestionDescription
     */
    public function getQuestionDescription(): ?QuestionDescription
    {
        return $this->questionDescription;
    }

    /**
     * @return SortOrder
     */
    public function getSortOrder(): ?SortOrder
    {
        return $this->sortOrder;
    }

    /**
     * @return QuestionTypeEnum
     */
    public function getType(): ?QuestionTypeEnum
    {
        return $this->type;
    }

    /**
     * @return StatusEnum
     */
    public function getStatus(): ?StatusEnum
    {
        return $this->status;
    }

    /**
     * @return DirectoryName|null
     */
    public function getDirectoryName(): ?DirectoryName
    {
        return $this->directoryName;
    }
}
