<?php
declare(strict_types=1);

namespace App\Repository;

use Tools\models\FutureObjectInterface;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Tools\models\ModelInterface;
use Tools\models\mappers\MapperInterface;
use Tools\collection\AbstractImmutableCollection;
use Tools\types\UuidType;

/**
 * This is the repository class for table "sms".
 */
abstract class AbstractCacheRepository implements RepositoryInterface
{
    /** @var MapperInterface */
    protected $mapper;

    /** @var CacheItemPoolInterface */
    protected $cache;

    /** @var RepositoryInterface */
    protected $parentRepository;

    /**
     * AbstractCacheRepository constructor.
     * @param CacheItemPoolInterface $cache
     * @param MapperInterface $mapper
     * @param RepositoryInterface $parentRepository
     */
    public function __construct(
        CacheItemPoolInterface $cache,
        MapperInterface $mapper,
        RepositoryInterface $parentRepository
    ) {
        $this->cache = $cache;
        $this->mapper = $mapper;
        $this->parentRepository = $parentRepository;
    }

    /**
     * @return UuidType
     * @throws DoctrineDBALDriverException
     * @throws DoctrineDBALException
     */
    public function getNewUuidId(): UuidType
    {
        return $this->parentRepository->getNewUuidId();
    }

    /**
     * @param string $collectionClass
     * @return AbstractImmutableCollection
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAll(string $collectionClass): AbstractImmutableCollection
    {
        $cacheItems = $this->cache->getItem($this->getCollectionKey());
        if ($cacheItems->isHit()) {
            $collection = $this->restoreCollection($cacheItems->get());
        } else {
            if (!method_exists($this->parentRepository, 'getAll')) {
                throw new MethodNotAllowedException(
                    [],
                    'You mast implement method `getAll` in parentRepository class:'
                    . get_class($this->parentRepository),
                    500
                );
            }
            $result = $this->parentRepository->getAll($collectionClass);
            $collection = new $collectionClass(function () use ($result) {
                return $this->storeCollection($result);
            });
        }
        return $collection;
    }

    /**
     * @param ModelInterface $model
     *
     * @return array
     */
    abstract protected function getKeys(ModelInterface $model): array;

    /**
     * @return string
     */
    abstract protected function getCollectionClass(): string;

    /**
     * @param string|null $identifier
     * @return string
     */
    protected function getCollectionKey(string $identifier = null): string
    {
        $class = (substr($this->getCollectionClass(), strrpos($this->getCollectionClass(), '\\') + 1));
        return $class . '_collection' . ($identifier ? '_' . $identifier : '');
    }

    /**
     * @param ModelInterface $model
     *
     * @return string
     */
    protected function prepareModel(ModelInterface $model): string
    {
        return json_encode($model->toArray());
    }

    /**
     * @param AbstractImmutableCollection $collection
     *
     * @return string
     */
    protected function prepareCollection(AbstractImmutableCollection $collection): string
    {
        $array = [];
        /** @var ModelInterface $item */
        foreach ($collection as $item) {
            $array[] = $item->toArray();
        }
        return json_encode($array);
    }

    /**
     * @param string $string
     *
     * @return ModelInterface
     */
    protected function restoreModel(string $string): ModelInterface
    {
        return $this->mapper->fromArray(json_decode($string, true));
    }

    /**
     * @param string $string
     *
     * @return AbstractImmutableCollection
     */
    protected function restoreCollection(string $string): AbstractImmutableCollection
    {
        $array = json_decode($string, true);
        foreach ($array as $i => $n) {
            $array[$i] = $this->mapper->fromArray($n);
        }
        $class = $this->getCollectionClass();
        return new $class($array);
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
        $collectionItem = $this->cache->getItem($this->getCollectionKey());
        if ($collectionItem->isHit()) {
            $this->removeCollection($this->getCollectionKey());
        }

        if ($model instanceof FutureObjectInterface) {
            $modelClass = get_class($model);
            return new $modelClass(function () use ($model) {
                /** @var ModelInterface $model */
                foreach ($this->getKeys($model) as $key) {
                    $cacheItem = $this->cache->getItem($key);
                    $this->cache->save($cacheItem->set($this->prepareModel($model)));
                }
                return $model->get();
            });
        }

        foreach ($this->getKeys($model) as $key) {
            $cacheItem = $this->cache->getItem($key);
            $this->cache->save($cacheItem->set($this->prepareModel($model)));
        }
        return $model;
    }

    /**
     * @param AbstractImmutableCollection $collection
     * @param string|null $identifier
     *
     * @return AbstractImmutableCollection
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    protected function storeCollection(AbstractImmutableCollection $collection, string $identifier = null)
    {
        $collectionClass = get_class($collection);
        return new $collectionClass(function () use ($identifier, $collection) {
            $cacheItem = $this->cache->getItem($this->getCollectionKey($identifier));
            $this->cache->save($cacheItem->set($this->prepareCollection($collection)));
            $array = [];
            foreach ($collection as $item) {
                $array[] = $this->storeToCache($item);
            }
            return $array;
        });
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
        foreach ($this->getKeys($model) as $key) {
            $cacheItem = $this->cache->getItem($key);
            if ($cacheItem->isHit()) {
                $this->cache->deleteItem($key);
            }
        }
        return $this->removeCollection($this->getCollectionKey());
    }

    /**
     * @param string|null $identifier
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    protected function removeCollection(string $key): bool
    {
        //$key = $this->getCollectionKey($identifier);
        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            $this->cache->deleteItem($key);
        }
        return true;
    }
}
