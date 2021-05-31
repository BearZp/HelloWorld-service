<?php

namespace App\Component\UserLevel;

use App\Component\BaseComponent;
use App\Component\Level\LevelComponentInterface;
use App\DataType\LevelId;
use App\DataType\UserId;
use App\Doctrine\Exception\NotFoundException;
use App\Model\UserLevel\UserLevel;
use App\Model\UserLevel\UserLevelInterface;
use App\Model\UserLevel\UserLevelStatusEnum;
use App\Repository\UserLevel\UserLevelRepositoryInterface;
use App\Request\UserLevel\CreateUserLevelModel;
use App\Request\UserLevel\SetUserLevelRequest;
use Exception;

class UserLevelComponent extends BaseComponent implements UserLevelComponentInterface
{
    /** @var UserLevelRepositoryInterface */
    protected $repository;

    /** @var LevelComponentInterface */
    protected $levelComponent;

    /**
     * UserLevelComponent constructor.
     * @param UserLevelRepositoryInterface $repository
     * @param LevelComponentInterface $levelComponent
     */
    public function __construct(
        UserLevelRepositoryInterface $repository,
        LevelComponentInterface $levelComponent
    ) {
        $this->repository = $repository;
        $this->levelComponent = $levelComponent;
    }

    /**
     * @param UserId $userId
     * @return UserLevelInterface
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getUserLevel(UserId $userId): UserLevelInterface
    {
        $userLevel = $this->repository->getByUserId($userId);
        try {
            $userId = $userLevel->getUserId();
        } catch (NotFoundException $e) {
            $userLevel = $this->createNewUserLevel($userId);
        }

        return $userLevel;
    }

    /**
     * @param UserId $userId
     * @return UserLevelInterface
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    protected function createNewUserLevel(UserId $userId): UserLevelInterface
    {
        $level = $this->levelComponent->getStartLevel();
        $nextLevel = $this->levelComponent->getSecondLevel();

        try {
            $levelId = $level->getId();
        } catch (NotFoundException $e) {
            throw new Exception('Can`t find start level for new user', 500);
        }

        try {
            $nextLevelId = $nextLevel->getId();
        } catch (NotFoundException $e) {
            $nextLevelId = new LevelId(0);
        }

        $userLevelRequest = new CreateUserLevelModel(
            $userId,
            $levelId,
            $nextLevelId,
            new UserLevelStatusEnum(UserLevelStatusEnum::VERIFIED)
        );

        return $this->repository->create($userLevelRequest);
    }

    /**
     * @param SetUserLevelRequest $setUserLevelRequest
     * @return UserLevelInterface
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function setUserLevel(SetUserLevelRequest $setUserLevelRequest): UserLevelInterface
    {
        $userLevel = $this->getUserLevel($setUserLevelRequest->getUserId());
        $level = $this->levelComponent->getLevelById($setUserLevelRequest->getLevelId());

        try {
            $newLevelId = $level->getId();
        } catch (NotFoundException $e) {
            throw new Exception('Try to set not existing level', 500);
        }

        $nextLevel = $this->levelComponent->findNextLevel($newLevelId);
        try {
            $userId = $userLevel->getUserId();
        } catch (NotFoundException $e) {
            $userLevel = $this->createNewUserLevel($setUserLevelRequest->getUserId());
        }

        try {
            $nextLevelId = $nextLevel->getId();
        } catch (NotFoundException $e) {
            $nextLevelId = $newLevelId;
        }

        return $this->repository->update(new UserLevel(
            $userLevel->getUserId(),
            $newLevelId,
            $nextLevelId,
            new UserLevelStatusEnum(UserLevelStatusEnum::VERIFIED),
            $userLevel->getMapper()
        ));
    }

    /**
     * @param UserId $userId
     * @param UserLevelStatusEnum $status
     * @return UserLevelInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function setUserLevelStatus(UserId $userId, UserLevelStatusEnum $status): UserLevelInterface
    {
        $userLevel = $this->repository->getByUserId($userId);
        $userLevel = $userLevel->withStatus($status);
        return $this->repository->update($userLevel);
    }

    /**
     * @param UserId $userId
     * @return UserLevelInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function upgrade(UserId $userId): UserLevelInterface
    {
        $userLevel = $this->repository->getByUserId($userId);

        if ($userLevel->getNextLevelId()->toInteger() === 0) {
            $this->getLogger()->alert(
                'Try to upgrade user on last level',
                [
                    'serviceName' => 'KYC',
                    'actionMane' => __METHOD__,
                    'object' => $userLevel,
                    'requestId' => $this->requestId
                ]
            );
            $newLevelId = $userLevel->getLevelId();
            $nextLevelId = $userLevel->getNextLevelId();
        } else {
            $newLevel = $this->levelComponent->getLevelById($userLevel->getNextLevelId());
            $nextLevel = $this->levelComponent->findNextLevel($userLevel->getNextLevelId());

            try {
                $newLevelId = $newLevel->getId();
            } catch (NotFoundException $e) {
                throw new Exception('Can`t find new level for user', 500);
            }

            try {
                $nextLevelId = $nextLevel->getId();
            } catch (NotFoundException $e) {
                $nextLevelId = new LevelId(0);
            }
        }

        $newUserLevel = new UserLevel(
            $userLevel->getUserId(),
            $newLevelId,
            $nextLevelId,
            new UserLevelStatusEnum(UserLevelStatusEnum::VERIFIED),
            $userLevel->getMapper()
        );

        return $this->repository->update($newUserLevel);
    }


    /**
     * @param UserId $userId
     * @param LevelId|null $targetLevelId
     * @return UserLevelInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function downgrade(UserId $userId, LevelId $targetLevelId = null): UserLevelInterface
    {
        $userLevel = $this->repository->getByUserId($userId);
        if (!$targetLevelId instanceof LevelId) {
            try {
                $parentLevel = $this->levelComponent->findParentLevel($userLevel->getLevelId());
                $targetLevelId = $parentLevel->getId();
                $nextUserLevelId = $userLevel->getLevelId();
            } catch (NotFoundException $e) {
                $this->getLogger()->alert(
                    'Try to downgrade below 0',
                    [
                        'serviceName' => 'KYC',
                        'actionMane' => __METHOD__,
                        'object' => $userLevel,
                        'requestId' => $this->requestId
                    ]
                );
                $targetLevelId = $userLevel->getLevelId();
                $nextUserLevelId = $userLevel->getNextLevelId();
            }
        } else {
            $nextUserLevel = $this->levelComponent->findNextLevel($targetLevelId);
            try {
                $nextUserLevelId = $nextUserLevel->getId();
            } catch (NotFoundException $e) {
                $nextUserLevelId = new LevelId(0);
            }
        }

        $newUserLevel = new UserLevel(
            $userLevel->getUserId(),
            $targetLevelId,
            $nextUserLevelId,
            new UserLevelStatusEnum(UserLevelStatusEnum::NEED_VERIFICATION),
            $userLevel->getMapper()
        );

        return $this->repository->update($newUserLevel);
    }
}
