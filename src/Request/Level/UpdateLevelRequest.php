<?php

namespace App\Request\Level;

use App\DataType\FirstLevelFlag;
use App\DataType\LevelAdvertisingText;
use App\DataType\LevelDescription;
use App\DataType\LevelName;
use App\DataType\LevelId;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Tools\types\BooleanType;
use Tools\types\IntegerType;
use Tools\types\StringType255;
use Tools\types\TextType;
use Symfony\Component\HttpFoundation\Request;

class UpdateLevelRequest extends AbstractRequest implements RequestInterface
{
    /** @var LevelId */
    protected $id;

    /** @var LevelName|null */
    protected $name;

    /** @var LevelDescription|null */
    protected $description;

    /** @var LevelAdvertisingText|null */
    protected $advertisingText;

    /** @var LevelId|null */
    protected $parentLevel;

    /** @var FirstLevelFlag|null */
    protected $isFirst;

    /**
     * CreateLevelRequest constructor.
     * @param Request $request
     * @throws RequestException
     */
    public function __construct(
        Request $request
    ) {
        parent::__construct($request);

        $id = $this->getFromRequest(
            'id',
            LevelId::class
        );

        $name = $this->getFromRequest(
            'name',
            LevelName::class,
            null
        );

        $description = $this->getFromRequest(
            'description',
            LevelDescription::class,
            null
        );

        $advertisingText = $this->getFromRequest(
            'advertising_text',
            LevelAdvertisingText::class,
            null
        );

        $parentLevel = $this->getFromRequest(
            'parent_level_id',
            LevelId::class,
            null
        );

        $isFirst = $this->getFromRequest(
            'is_first',
            FirstLevelFlag::class,
            null
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->advertisingText = $advertisingText;
        $this->parentLevel = $parentLevel;
        $this->isFirst = $isFirst;
    }

    /**
     * @return LevelId
     */
    public function getId(): LevelId
    {
        return $this->id;
    }

    /**
     * @return LevelName|null
     */
    public function getName(): ?LevelName
    {
        return $this->name;
    }

    /**
     * @return LevelDescription|null
     */
    public function getDescription(): ?LevelDescription
    {
        return $this->description;
    }

    /**
     * @return LevelAdvertisingText|null
     */
    public function getAdvertisingText(): ?LevelAdvertisingText
    {
        return $this->advertisingText;
    }

    /**
     * @return LevelId|null
     */
    public function getParentLevel(): ?LevelId
    {
        return $this->parentLevel;
    }

    /**
     * @return FirstLevelFlag|null
     */
    public function getIsFirst(): ?FirstLevelFlag
    {
        return $this->isFirst;
    }
}
