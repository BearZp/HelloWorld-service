<?php

namespace App\Repository\UserLevel;

use App\DataType\UserId;
use App\Model\UserLevel\UserLevelCollection;
use App\Model\UserLevel\UserLevelFuture;
use App\Model\UserLevel\UserLevelInterface;
use App\Repository\AbstractCacheRepository;
use App\Request\UserLevel\CreateUserLevelModel;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Tools\models\ModelInterface;

class UserLevelCacheRepository extends AbstractCacheRepository implements UserLevelRepositoryInterface
{
    /**
     * @param UserId $userId
     * @return UserLevelInterface
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getByUserId(UserId $userId): UserLevelInterface
    {
        $cacheItem = $this->cache->getItem($this->getUserIdKey($userId));
        if ($cacheItem->isHit()) {
            /** @var UserLevelInterface $model */
            $model = $this->restoreModel($cacheItem->get());
            return $model;
        }
        return new UserLevelFuture(function () use ($userId) {
            return $this->storeToCache($this->parentRepository->getByUserId($userId));
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
        return $this->storeToCache($this->parentRepository->create($userLevelRequest));
    }

    /**
     * @param UserLevelInterface $userLevel
     * @return UserLevelInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function update(UserLevelInterface $userLevel): UserLevelInterface
    {
        $key = $this->getUserIdKey($userLevel->getUserId());
        $this->cache->deleteItem($key);
        return $this->storeToCache($this->parentRepository->update($userLevel));
    }

    /**
     * @param UserId $userId
     * @return string
     */
    private function getUserIdKey(UserId $userId): string
    {
        $class = (substr(self::class, strrpos(self::class, '\\') + 1));
        return  $class . '_user_id_' . $userId->toString();
    }

    /**
     * @param ModelInterface $model
     * @return string[]
     */
    protected function getKeys(ModelInterface $model): array
    {
        /** @var UserLevelInterface $model */
        return [
            $this->getUserIdKey($model->getUserId())
        ];
    }

    /**
     * @return string
     */
    protected function getCollectionClass(): string
    {
        return UserLevelCollection::class;
    }
}
