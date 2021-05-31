<?php

namespace App\Request\UserLevel;

use App\DataType\UserId;
use App\Request\AbstractRequest;
use App\Request\RequestException;
use App\Request\RequestInterface;
use Symfony\Component\HttpFoundation\Request;

class ByUserIdRequest extends AbstractRequest implements RequestInterface
{
    /** @var UserId */
    protected $userId;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $userId = $this->getFromRequest(
            'user_id',
            UserId::class
        );

        if ($this->errors) {
            throw new RequestException($this->errors);
        }

        $this->userId = $userId;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }
}
