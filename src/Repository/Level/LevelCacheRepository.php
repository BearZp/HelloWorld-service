<?php

namespace App\Repository\Level;

use App\DataType\LevelId;
use App\Model\Level\LevelCollection;
use App\Model\Level\LevelFuture;
use App\Model\Level\LevelInterface;
use App\Repository\AbstractCacheRepository;
use App\Request\Level\CreateLevelRequest;
use App\Request\Level\UpdateLevelRequest;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Tools\models\FutureObjectTrait;
use Tools\models\ModelInterface;
use Tools\types\IntegerType;
use Doctrine\Common\Collections\Criteria;

class LevelCacheRepository extends AbstractCacheRepository implements LevelRepositoryInterface
{
    /**
     * @return LevelInterface
     */
    public function getStartLevel(): LevelInterface
    {
        return $this->parentRepository->getStartLevel();
    }

    /**
     * @return LevelInterface
     */
    public function getSecondLevel(): LevelInterface
    {
        return $this->parentRepository->getSecondLevel();
    }

    /**
     * @param LevelId $id
     * @return LevelInterface
     */
    public function findNextLevel(LevelId $id): LevelInterface
    {
        $cacheItem = $this->cache->getItem($this->getNextIdKey($id));
        if ($cacheItem->isHit()) {
            /** @var LevelInterface $model */
            $model = $this->restoreModel($cacheItem->get());
        } else {
            $level = $this->parentRepository->findNextLevel($id);
            $model = $this->storeToCache($level);
        }
        return $model;
    }

    /**
     * @param LevelId $id
     * @return LevelInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function findParentLevel(LevelId $id): LevelInterface
    {
        return $this->parentRepository->findParentLevel($id);
    }

    /**
     * @return LevelCollection
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAllLevels(): LevelCollection
    {
        /** @var LevelCollection  $result */
        $result = parent::getAll($this->getCollectionClass());
        return $result;
    }

    /**
     * @param LevelId $id
     * @return LevelInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function getById(LevelId $id): LevelInterface
    {
        $cacheItem = $this->cache->getItem($this->getIdKey($id));
        if ($cacheItem->isHit()) {
            /** @var LevelInterface $model */
            $model = $this->restoreModel($cacheItem->get());
            return $model;
        }
        $level = $this->parentRepository->getById($id);
        return $this->storeToCache($level);
    }

    /**
     * @param LevelId $id
     * @return LevelInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function deleteById(LevelId $id): bool
    {
        $result = $this->parentRepository->deleteById($id);
        if ($result) {
            $this->cache->deleteItem($this->getIdKey($id));
            $this->cache->deleteItem($this->getNextIdKey($id));
        }
        return $result;
    }

    /**
     * @param CreateLevelRequest $createLevelRequest
     * @return LevelInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function create(CreateLevelRequest $createLevelRequest): LevelInterface
    {
        return $this->storeToCache($this->parentRepository->create($createLevelRequest));
    }

    /**
     * @param LevelInterface $level
     * @return LevelInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function update(LevelInterface $level): LevelInterface
    {
        $this->cache->deleteItem($this->getIdKey($level->getId()));
        return $this->storeToCache($this->parentRepository->update($level));
    }

    /**
     * @param LevelId $id
     * @return string
     */
    private function getIdKey(LevelId $id): string
    {
        $class = (substr(self::class, strrpos(self::class, '\\') + 1));
        return  $class . '_id_' . $id->toString(); // LevelModel_id_2
    }

    /**
     * @param LevelId $id
     * @return string
     */
    private function getNextIdKey(LevelId $id): string
    {
        $class = (substr(self::class, strrpos(self::class, '\\') + 1));
        return  $class . '_next_id_' . $id->toString(); // LevelModel_next_for_id_1
    }

    /**
     * @param ModelInterface $model
     * @return string[]
     */
    protected function getKeys(ModelInterface $model): array
    {
        /** @var LevelInterface $model */
        $result = [
            $this->getIdKey($model->getId()),
        ];
        $parentLevel = $model->getParentLevel();
        if ($parentLevel->toInteger() > 0) {
            $result[] = $this->getNextIdKey($parentLevel);
        }

        return $result;
    }

    /**
     * @return string
     */
    protected function getCollectionClass(): string
    {
        return LevelCollection::class;
    }
}
