<?php

namespace App\Request\Question;

use App\DataType\DirectoryName;
use App\DataType\LevelId;
use App\DataType\QuestionDescription;
use App\DataType\QuestionId;
use App\DataType\QuestionTitle;
use App\DataType\SortOrder;
use App\Model\Level\LevelIdCollection;
use App\Model\StatusEnum;
use App\Model\Question\QuestionTypeEnum;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;
use Tools\types\IntegerType;
use Tools\types\StringType255;
use Tools\types\UuidType;

class UpdateQuestionRequest extends AbstractRequest implements RequestInterface
{
    /** @var QuestionId */
    protected $id;

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

    /** @var DirectoryName */
    protected $directoryName;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $id = $this->getFromRequest(
            'id',
            QuestionId::class
        );
        $levels = $this->getCollectionFromRequest(
            'levels',
            LevelIdCollection::class,
            LevelId::class
        );
        $questionTitle = $this->getFromRequest(
            'question_title',
            QuestionTitle::class,
            null
        );
        $questionDescription = $this->getFromRequest(
            'question_description',
            QuestionDescription::class,
            null
        );
        $sortOrder = $this->getFromRequest(
            'sort_order',
            SortOrder::class,
            new SortOrder(0)
        );
        $type = $this->getFromRequest(
            'type',
            QuestionTypeEnum::class,
            null
        );
        $status = $this->getFromRequest(
            'status',
            StatusEnum::class,
            null
        );
        $directoryName = $this->getFromRequest(
            'directory_name',
            DirectoryName::class,
            null
        );

        $selectType = new QuestionTypeEnum(QuestionTypeEnum::SELECT);
        if ($type && $selectType->isEqual($type) && $directoryName === null) {
            $this->errors[] = 'Directory name is mandatory for question type ' . $selectType->getKey();
        }

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->id = $id;
        $this->levels = $levels;
        $this->questionTitle = $questionTitle;
        $this->questionDescription = $questionDescription;
        $this->sortOrder = $sortOrder;
        $this->type = $type;
        $this->status = $status;
        $this->directoryName = $directoryName;
    }

    /**
     * @return QuestionId
     */
    public function getId(): QuestionId
    {
        return $this->id;
    }

    /**
     * @return LevelIdCollection|null
     */
    public function getLevelIdCollection(): ?LevelIdCollection
    {
        return $this->levels;
    }

    /**
     * @return QuestionTitle|null
     */
    public function getQuestionTitle(): ?QuestionTitle
    {
        return $this->questionTitle;
    }

    /**
     * @return QuestionDescription|null
     */
    public function getQuestionDescription(): ?QuestionDescription
    {
        return $this->questionDescription;
    }

    /**
     * @return SortOrder|null
     */
    public function getSortOrder(): ?SortOrder
    {
        return $this->sortOrder;
    }

    /**
     * @return QuestionTypeEnum|null
     */
    public function getType(): ?QuestionTypeEnum
    {
        return $this->type;
    }

    /**
     * @return StatusEnum|null
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
