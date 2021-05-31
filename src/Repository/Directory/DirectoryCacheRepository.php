<?php

namespace App\Repository\Directory;

use App\DataType\DirectoryId;
use App\DataType\DirectoryName;
use App\Model\Directory\DirectoryCollection;
use App\Model\Directory\DirectoryFuture;
use App\Model\Directory\DirectoryInterface;
use App\Repository\AbstractCacheRepository;
use App\Request\Directory\CreateDirectoryRequest;
use Tools\collection\AbstractImmutableCollection;
use Tools\models\FutureObjectTrait;
use Tools\models\ModelInterface;

class DirectoryCacheRepository extends AbstractCacheRepository implements DirectoryRepositoryInterface
{
    /**
     * @return DirectoryCollection
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAllDirectories(): DirectoryCollection
    {
        /** @var DirectoryCollection  $result */
        $result = parent::getAll($this->getCollectionClass());
        return $result;
    }

    /**
     * @param DirectoryId $id
     * @return DirectoryInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getById(DirectoryId $id): DirectoryInterface
    {
        $cacheItem = $this->cache->getItem($this->getIdKey($id));
        if ($cacheItem->isHit()) {
            /** @var DirectoryInterface $model */
            $model = $this->restoreModel($cacheItem->get());
        } else {
            $directory = $this->parentRepository->getById($id);
            $model = new DirectoryFuture(function () use ($directory) {
                return $this->storeToCache($directory);
            });
        }
        return $model;
    }

    /**
     * @param DirectoryName $directoryName
     * @return DirectoryCollection
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getDirectoryByName(DirectoryName $directoryName): DirectoryCollection
    {
        $cacheItems = $this->cache->getItem($this->getCollectionKey($directoryName->toString()));
        if ($cacheItems->isHit()) {
            $collection = $this->restoreCollection($cacheItems->get());
        } else {
            $result = $this->parentRepository->getDirectoryByName($directoryName);
            $collection = new DirectoryCollection(function () use ($directoryName, $result) {
                return $this->storeCollection(
                    $result,
                    $directoryName->toString()
                );
            });
        }
        /** @var DirectoryCollection $collection */
        return $collection;
    }

    /**
     * @param CreateDirectoryRequest $createDirectoryRequest
     * @return DirectoryInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function create(CreateDirectoryRequest $createDirectoryRequest): DirectoryInterface
    {
        return $this->storeToCache($this->parentRepository->create($createDirectoryRequest));
    }

    /**
     * @param DirectoryInterface $directory
     * @return DirectoryInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function update(DirectoryInterface $directory): DirectoryInterface
    {
        $this->removeFromCache($directory);
        return $this->storeToCache($this->parentRepository->update($directory));
    }

    /**
     * @param DirectoryId $id
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteById(DirectoryId $id): bool
    {
        $result = $this->parentRepository->deleteById($id);
        if ($result) {
            $this->cache->deleteItem($this->getIdKey($id));
        }

        return $result;
    }

    /**
     * @param DirectoryId $id
     * @return string
     */
    private function getIdKey(DirectoryId $id): string
    {
        $class = (substr(self::class, strrpos(self::class, '\\') + 1));
        return  $class . '_id_' . $id->toString();
    }

    /**
     * @param ModelInterface $model
     * @return string[]
     */
    protected function getKeys(ModelInterface $model): array
    {
        /** @var DirectoryInterface $model */
        return [
            $this->getIdKey($model->getId())
        ];
    }

    /**
     * @return string
     */
    protected function getCollectionClass(): string
    {
        return DirectoryCollection::class;
    }

    /**
     * @param ModelInterface $model
     *
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    protected function storeToCache(ModelInterface $model)
    {
        /** @var DirectoryInterface $model */
        $collectionKey = $this->getCollectionKey($model->getDirectoryName()->toString());
        $this->removeCollection($collectionKey);

        $listItem = $this->cache->getItem('list');
        if ($listItem->isHit()) {
            $list = json_decode($listItem->get(), true);
            if (!in_array($model->getDirectoryName()->toString(), $list)) {
                $this->cache->deleteItem('list');
            }
        }

        return parent::storeToCache($model);
    }

    /**
     * @param ModelInterface $model
     *
     * @return mixed
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    protected function removeFromCache(ModelInterface $model): bool
    {
        /** @var DirectoryInterface $model */
        $collectionKey = $this->getCollectionKey($model->getDirectoryName()->toString());
        $collectionItem = $cacheItem = $this->cache->getItem($collectionKey);
        if ($collectionItem->isHit()) {
            $this->removeCollection($collectionKey);
        }
        $this->cache->deleteItem('list');

        return parent::removeFromCache($model);
    }
}
