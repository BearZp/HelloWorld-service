<?php

namespace App\Repository\Question;

use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\Model\Level\LevelCollection;
use App\Model\Question\QuestionCollection;
use App\Model\Question\QuestionFuture;
use App\Model\Question\QuestionInterface;
use App\Repository\AbstractCacheRepository;
use Tools\collection\AbstractImmutableCollection;
use Tools\models\ModelInterface;

class QuestionCacheRepository extends AbstractCacheRepository implements QuestionRepositoryInterface
{
    /**
     * @param QuestionId $id
     * @return QuestionInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getById(QuestionId $id): QuestionInterface
    {
        $cacheItem = $this->cache->getItem($this->getIdKey($id));
        if ($cacheItem->isHit()) {
            /** @var QuestionInterface $model */
            $model = $this->restoreModel($cacheItem->get());
        } else {
            $question = $this->parentRepository->getById($id);
            $model = new QuestionFuture(function () use ($question) {
                return $this->storeToCache($question);
            });
        }
        return $model;
    }

    /**
     * @param QuestionInterface $question
     * @return QuestionInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function create(QuestionInterface $question): QuestionInterface
    {
        return $this->storeToCache($this->parentRepository->create($question));
    }

    /**
     * @param QuestionInterface $question
     * @return QuestionInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function update(QuestionInterface $question): QuestionInterface
    {
        $this->removeFromCache($question);
        return $this->storeToCache($this->parentRepository->update($question));
    }

    /**
     * @param QuestionId $id
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteById(QuestionId $id): bool
    {
        $result = $this->parentRepository->deleteById($id);
        if ($result) {
            $this->cache->deleteItem($this->getIdKey($id));
        }

        return $result;
    }

    /**
     * @param QuestionId $id
     * @return string
     */
    private function getIdKey(QuestionId $id): string
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
        /** @var QuestionInterface $model */
        return [
            $this->getIdKey($model->getId())
        ];
    }

    /**
     * @return string
     */
    protected function getCollectionClass(): string
    {
        return QuestionCollection::class;
    }

    /**
     * @return QuestionCollection
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAllQuestions(): QuestionCollection
    {
        /** @var QuestionCollection $result */
        $result = parent::getAll($this->getCollectionClass());
        return $result;
    }
}
