<?php

namespace App\Repository\UserLevel;

use App\DataType\UserId;
use App\doctrine\pgsql\Connection;
use App\Model\UserLevel\UserLevel;
use App\Model\UserLevel\UserLevelFuture;
use App\Model\UserLevel\UserLevelInterface;
use App\Model\UserLevel\UserLevelMapper;
use App\Repository\AbstractRepository;
use App\Request\UserLevel\CreateUserLevelModel;
use \Doctrine\DBAL\Exception as DoctrineDBALException;
use \Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Exception;

class UserLevelRepository extends AbstractRepository implements UserLevelRepositoryInterface
{
    protected const TABLE = 'kyc_user_level';

    /**
     * CountryRepository constructor.
     * @param Connection $connection
     * @param UserLevelMapper $mapper
     */
    public function __construct(Connection $connection, UserLevelMapper $mapper)
    {
        parent::__construct($connection, $mapper);
    }

    /**
     * @param UserId $userId
     * @return UserLevelInterface
     * @throws DoctrineDBALException
     */
    public function getByUserId(UserId $userId): UserLevelInterface
    {
        $query = $this->getBuilder()
            ->select('*')
            ->from(self::TABLE)
            ->where('user_id = :userId')
            ->setParameter(':userId', $userId->toString())
            ->execute();

        return new UserLevelFuture(function () use ($query) {
            return $this->mapper->fromArray($query->fetchAssociative());
        });
    }

    /**
     * @param CreateUserLevelModel $userLevelRequest
     * @return UserLevelInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(CreateUserLevelModel $userLevelRequest): UserLevelInterface
    {
        $query = $this->getBuilder()
            ->insert(self::TABLE)
            ->values([
                'user_id' => ':userId',
                'level_id' => ':levelId',
                'next_level_id' => ':nextLevel',
                'next_level_status' => ':status',
            ])
            ->setParameter(':userId', $userLevelRequest->getUserId()->toString())
            ->setParameter(':levelId', $userLevelRequest->getLevelId()->toInteger())
            ->setParameter(':nextLevel', $userLevelRequest->getNextLevelId()->toInteger() ?: null)
            ->setParameter(':status', $userLevelRequest->getStatus()->getValue())
            ->execute();
        if ($query) {
            return new UserLevel(
                $userLevelRequest->getUserId(),
                $userLevelRequest->getLevelId(),
                $userLevelRequest->getNextLevelId(),
                $userLevelRequest->getStatus(),
                $this->mapper
            );
        }

        throw new Exception('Can`t save UserLevel', 500);
    }

    /**
     * @param UserLevelInterface $userLevel
     * @return UserLevelInterface
     * @throws DoctrineDBALException
     */
    public function update(UserLevelInterface $userLevel): UserLevelInterface
    {
        $query = $this->getBuilder()
            ->update(self::TABLE)
            ->set('level_id', ':level_id')
            ->set('next_level_id', ':next_level_id')
            ->set('next_level_status', ':status')
            ->setParameter(':level_id', $userLevel->getLevelId()->toInteger())
            ->setParameter(':next_level_id', $userLevel->getNextLevelId()->toInteger())
            ->setParameter(':status', $userLevel->getStatus()->getValue())
            ->execute();

        if ($query) {
            return $userLevel;
        }

        throw new Exception('Can`t update UserLevel', 500);
    }
}
