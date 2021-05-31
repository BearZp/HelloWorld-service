<?php

namespace App\Request\Answer;

use App\DataType\LevelId;
use App\DataType\UserId;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ByUserAndLevelRequest extends AbstractRequest implements RequestInterface
{
    /** @var UserId */
    protected $userId;

    /** @var LevelId */
    protected $levelId;

    /**
     * ByUserAndLevelRequest constructor.
     * @param Request $request
     * @throws RequestException
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);

        $userId = $this->getFromRequest(
            'user_id',
            UserId::class
        );
        $levelId = $this->getFromRequest(
            'level_id',
            LevelId::class
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->userId = $userId;
        $this->levelId = $levelId;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return LevelId
     */
    public function getLevelId(): LevelId
    {
        return $this->levelId;
    }
}
