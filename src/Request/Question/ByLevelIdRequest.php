<?php

namespace App\Request\Question;

use App\DataType\LevelId;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ByLevelIdRequest extends AbstractRequest implements RequestInterface
{
    /** @var LevelId */
    protected $id;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $id = $this->getFromRequest(
            'level_id',
            LevelId::class
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->id = $id;
    }

    /**
     * @return LevelId
     */
    public function getId(): LevelId
    {
        return $this->id;
    }
}
