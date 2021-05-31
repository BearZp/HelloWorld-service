<?php

namespace App\Repository\UserLevel;

use App\DataType\UserId;
use App\Model\UserLevel\UserLevelInterface;
use App\Repository\RepositoryInterface;
use App\Request\UserLevel\CreateUserLevelModel;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Doctrine\DBAL\Exception as DoctrineDBALException;

interface UserLevelRepositoryInterface extends RepositoryInterface
{
    /**
     * @param UserId $userId
     * @return UserLevelInterface
     * @throws DoctrineDBALException
     */
    public function getByUserId(UserId $userId): UserLevelInterface;

    /**
     * @param CreateUserLevelModel $userLevelRequest
     * @return UserLevelInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(CreateUserLevelModel $userLevelRequest): UserLevelInterface;

    /**
     * @param UserLevelInterface $userLevel
     * @return UserLevelInterface
     * @throws DoctrineDBALException
     */
    public function update(UserLevelInterface $userLevel): UserLevelInterface;
}
