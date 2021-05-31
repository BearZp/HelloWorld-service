<?php

namespace App\Repository\Answer;

use App\DataType\AnswerId;
use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\DataType\UserId;
use App\Model\Answer\AnswerCollection;
use App\Model\Answer\AnswerFuture;
use App\Model\Answer\AnswerInterface;
use App\Model\Answer\AnswerStatusEnum;
use App\Repository\AbstractCacheRepository;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Tools\models\FutureObjectTrait;
use Tools\models\ModelInterface;

class AnswerCacheRepository extends AbstractCacheRepository implements AnswerRepositoryInterface
{
    /**
     * @param AnswerId $id
     * @return AnswerInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getById(AnswerId $id): AnswerInterface
    {
        $cacheItem = $this->cache->getItem($this->getIdKey($id));
        if ($cacheItem->isHit()) {
            /** @var AnswerInterface $model */
            $model = $this->restoreModel($cacheItem->get());
        } else {
            $answer = $this->parentRepository->getById($id);
            $model = new AnswerFuture(function () use ($answer) {
                return $this->storeToCache($answer);
            });
        }
        return $model;
    }

    /**
     * @param QuestionId $questionId
     * @param UserId $userId
     * @return AnswerInterface
     * @throws DoctrineDBALException
     */
    public function getAnswerForQuestion(QuestionId $questionId, UserId $userId): AnswerInterface
    {
        $cacheItem = $this->cache->getItem($this->getQuestionKey($questionId, $userId));
        if ($cacheItem->isHit()) {
            /** @var AnswerInterface $model */
            $model = $this->restoreModel($cacheItem->get());
            return $model;
        }

        $answer = $this->parentRepository->getAnswerForQuestion($questionId, $userId);
        return new AnswerFuture(function () use ($answer) {
            return $this->storeToCache($answer);
        });
    }

    /**
     * @param UserId $userId
     * @return AnswerCollection
     * @throws DoctrineDBALException
     */
    public function getAnswersForUser(UserId $userId): AnswerCollection
    {
        $cacheItems = $this->cache->getItem($this->getCollectionKey($userId->toString()));
        if ($cacheItems->isHit()) {
            $collection = $this->restoreCollection($cacheItems->get());
        } else {
            $result = $this->parentRepository->getAnswersForUser($userId);
            $collection = new AnswerCollection(function () use ($userId, $result) {
                return $this->storeCollection(
                    $result,
                    $userId->toString()
                );
            });
        }
        /** @var AnswerCollection $collection */
        return $collection;
    }

    /**
     * @param AnswerInterface $question
     * @return AnswerInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function create(AnswerInterface $question): AnswerInterface
    {
        return $this->storeToCache($this->parentRepository->create($question));
    }

    /**
     * @param AnswerInterface $question
     * @return AnswerInterface
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function update(AnswerInterface $question): AnswerInterface
    {
        $this->removeFromCache($question);
        return $this->storeToCache($this->parentRepository->update($question));
    }

    /**
     * @param AnswerId $id
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function deleteById(AnswerId $id): bool
    {
        $result = $this->parentRepository->deleteById($id);
        if ($result) {
            $this->cache->deleteItem($this->getIdKey($id));
        }

        return $result;
    }

    /**
     * @param AnswerId $id
     * @return string
     */
    private function getIdKey(AnswerId $id): string
    {
        $class = (substr(self::class, strrpos(self::class, '\\') + 1));
        return  $class . '_id_' . $id->toString();
    }

    /**
     * @param QuestionId $questionId
     * @return string
     */
    private function getQuestionKey(QuestionId $questionId, UserId $userId): string
    {
        $class = (substr(self::class, strrpos(self::class, '\\') + 1));
        return  $class . '_question_' . $questionId->toString() . '_user_' . $userId->toString();
    }

    /**
     * @param ModelInterface $model
     * @return string[]
     */
    protected function getKeys(ModelInterface $model): array
    {
        /** @var AnswerInterface $model */
        return [
            $this->getIdKey($model->getId()),
            $this->getQuestionKey($model->getQuestionId(), $model->getUserId())
        ];
    }

    /**
     * @return string
     */
    protected function getCollectionClass(): string
    {
        return AnswerCollection::class;
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
        /** @var AnswerInterface $model */
        $collectionKey = $this->getCollectionKey($model->getUserId()->toString());
        $collectionItem = $this->cache->getItem($collectionKey);
        if ($collectionItem->isHit()) {
            $this->removeCollection($collectionKey);
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
        /** @var AnswerInterface $model */
        $collectionKey = $this->getCollectionKey($model->getUserId()->toString());
        $collectionItem = $cacheItem = $this->cache->getItem($collectionKey);
        if ($collectionItem->isHit()) {
            $this->removeCollection($collectionKey);
        }

        return parent::removeFromCache($model);
    }
}
