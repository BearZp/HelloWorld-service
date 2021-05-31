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

class CreateLevelRequest extends AbstractRequest implements RequestInterface
{
    /** @var LevelName */
    protected $name;

    /** @var LevelDescription */
    protected $description;

    /** @var LevelAdvertisingText */
    protected $advertisingText;

    /** @var LevelId */
    protected $parentLevel;

    /** @var FirstLevelFlag */
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


        $name = $this->getFromRequest(
            'name',
            LevelName::class
        );

        $description = $this->getFromRequest(
            'description',
            LevelDescription::class
        );

        $advertisingText = $this->getFromRequest(
            'advertising_text',
            LevelAdvertisingText::class
        );

        $parentLevel = $this->getFromRequest(
            'parent_level_id',
            LevelId::class,
            new LevelId(0)
        );

        $isFirst = $this->getFromRequest(
            'is_first',
            FirstLevelFlag::class,
            new FirstLevelFlag(false)
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->name = $name;
        $this->description = $description;
        $this->advertisingText = $advertisingText;
        $this->parentLevel = $parentLevel;
        $this->isFirst = $isFirst;
    }

    /**
     * @return LevelName
     */
    public function getName(): LevelName
    {
        return $this->name;
    }

    /**
     * @return LevelDescription
     */
    public function getDescription(): LevelDescription
    {
        return $this->description;
    }

    /**
     * @return LevelAdvertisingText
     */
    public function getAdvertisingText(): LevelAdvertisingText
    {
        return $this->advertisingText;
    }

    /**
     * @return LevelId
     */
    public function getParentLevel(): LevelId
    {
        return $this->parentLevel;
    }

    /**
     * @return FirstLevelFlag
     */
    public function getIsFirst(): FirstLevelFlag
    {
        return $this->isFirst;
    }
}
