<?php

namespace App\Repository\Question;

use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\Model\Level\LevelCollection;
use App\Model\Level\LevelMapper;
use App\Model\Question\QuestionCollection;
use App\Model\Question\QuestionFuture;
use App\Model\Question\QuestionInterface;
use App\Model\Question\QuestionMapper;
use App\Repository\AbstractCacheRepository;
use App\Repository\RepositoryInterface;
use Psr\Cache\CacheItemPoolInterface;
use Tools\collection\AbstractImmutableCollection;
use Tools\models\mappers\MapperInterface;
use Tools\models\ModelInterface;

class QuestionLevelCacheRepository extends AbstractCacheRepository implements QuestionLevelRepositoryInterface
{
    /** @var QuestionMapper */
    protected $questionMapper;

    /** @var LevelMapper */
    protected $levelMapper;

    /**
     * QuestionLevelCacheRepository constructor.
     * @param CacheItemPoolInterface $cache
     * @param QuestionMapper $questionMapper
     * @param LevelMapper $levelMapper
     * @param RepositoryInterface $parentRepository
     */
    public function __construct(
        CacheItemPoolInterface $cache,
        QuestionMapper $questionMapper,
        LevelMapper $levelMapper,
        RepositoryInterface $parentRepository
    ) {
        parent::__construct($cache, $questionMapper, $parentRepository);
        $this->questionMapper = $questionMapper;
        $this->levelMapper = $levelMapper;
    }

    /**
     * @param QuestionId $questionId
     * @return LevelCollection
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getLevelsForQuestion(QuestionId $questionId): LevelCollection
    {
        $key =  $this->getQuestionKey($questionId);
        $cacheItems = $this->cache->getItem($key);
        if ($cacheItems->isHit()) {
            $array = json_decode($cacheItems->get(), true);
            foreach ($array as $i => $n) {
                $array[$i] = $this->levelMapper->fromArray($n);
            }
            $collection =  new LevelCollection($array);
        } else {
            $result = $this->parentRepository->getLevelsForQuestion($questionId);
            $collection = new LevelCollection(function () use ($key, $result) {
                $items = [];
                if ($result->count() !== 0) {
                    $array = [];
                    foreach ($result as $item) {
                        $items[] = $item;
                        $array[] = $item->toArray();
                    }
                    $cacheItem = $this->cache->getItem($key);
                    $this->cache->save($cacheItem->set(json_encode($array)));
                }
                return $items;
            });
        }
        return $collection;
    }

    /**
     * @param LevelId $levelId
     * @return QuestionCollection
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getQuestionsForLevel(LevelId $levelId): QuestionCollection
    {
        $key =  $this->getLevelKey($levelId);
        $cacheItems = $this->cache->getItem($key);
        if ($cacheItems->isHit()) {
            $array = json_decode($cacheItems->get(), true);
            foreach ($array as $i => $n) {
                $array[$i] = $this->questionMapper->fromArray($n);
            }
            $collection =  new QuestionCollection($array);
        } else {
            $result = $this->parentRepository->getQuestionsForLevel($levelId);
            $collection = new QuestionCollection(function () use ($key, $result) {
                $items = [];
                if ($result->count() !== 0) {
                    $array = [];
                    foreach ($result as $item) {
                        $items[] = $item;
                        $array[] = $item->toArray();
                    }
                    $cacheItem = $this->cache->getItem($key);
                    $this->cache->save($cacheItem->set(json_encode($array)));
                }

                return $items;
            });
        }
        return $collection;
    }

    /**
     * @param QuestionId $questionId
     * @param LevelId $levelId
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function attachQuestionToLevel(QuestionId $questionId, LevelId $levelId): bool
    {
        $this->cache->deleteItem($this->getLevelKey($levelId));
        $this->cache->deleteItem($this->getQuestionKey($questionId));
        return $this->parentRepository->attachQuestionToLevel($questionId, $levelId);
    }

    /**
     * @param QuestionId $questionId
     * @param LevelId $levelId
     * @return bool
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function detachQuestionFromLevel(QuestionId $questionId, LevelId $levelId): bool
    {
        $this->cache->deleteItem($this->getLevelKey($levelId));
        $this->cache->deleteItem($this->getQuestionKey($questionId));
        return $this->parentRepository->detachQuestionFromLevel($questionId, $levelId);
    }

    /**
     * @param ModelInterface $model
     * @return string[]
     */
    protected function getKeys(ModelInterface $model): array
    {
        return [];
    }

    /**
     * @param LevelId $levelId
     * @return string
     */
    protected function getLevelKey(LevelId $levelId): string
    {
        return 'QuestionCollection_For_Level_' . $levelId->toString();
    }

    /**
     * @param QuestionId $questionId
     * @return string
     */
    protected function getQuestionKey(QuestionId $questionId): string
    {
        return 'LevelCollection_For_Question_' . $questionId->toString();
    }

    /**
     * @return string
     */
    protected function getCollectionClass(): string
    {
        return QuestionCollection::class;
    }
}
