<?php

namespace App\Component\Question;

use App\Component\BaseComponent;
use App\Component\Directory\DirectoryComponentInterface;
use App\Component\Level\LevelComponent;
use App\Component\Level\LevelComponentInterface;
use App\DataType\LevelId;
use App\DataType\QuestionId;
use App\Doctrine\Exception\NotFoundException;
use App\Model\Directory\DirectoryInterface;
use App\Model\Level\Level;
use App\Model\Level\LevelCollection;
use App\Model\Level\LevelIdCollection;
use App\Model\Question\Question;
use App\Model\Question\QuestionCollection;
use App\Model\Question\QuestionInterface;
use App\Model\Question\QuestionMapper;
use App\Model\Question\QuestionTypeEnum;
use App\Repository\Question\QuestionLevelRepositoryInterface;
use App\Repository\Question\QuestionRepositoryInterface;
use App\Request\Question\CreateQuestionRequest;
use App\Request\Question\UpdateQuestionRequest;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Exception;

class QuestionComponent extends BaseComponent implements QuestionComponentInterface
{
    /** @var QuestionRepositoryInterface */
    protected $repository;

    /** @var QuestionLevelRepositoryInterface */
    protected $relationRepository;

    /** @var LevelComponentInterface */
    protected $levelComponent;

    /** @var DirectoryComponentInterface */
    protected $directoryComponent;

    /**
     * UserLevelComponent constructor.
     * @param QuestionRepositoryInterface $repository
     * @param QuestionLevelRepositoryInterface $relationRepository
     * @param LevelComponentInterface $levelComponent
     * @param DirectoryComponentInterface $directoryComponent
     */
    public function __construct(
        QuestionRepositoryInterface $repository,
        QuestionLevelRepositoryInterface $relationRepository,
        LevelComponentInterface $levelComponent,
        DirectoryComponentInterface $directoryComponent
    ) {
        $this->repository = $repository;
        $this->relationRepository = $relationRepository;
        $this->levelComponent = $levelComponent;
        $this->directoryComponent = $directoryComponent;
    }

    /**
     * @param QuestionId $id
     * @return QuestionInterface
     * @throws DoctrineDBALException
     */
    public function getById(QuestionId $id): QuestionInterface //TODO: can be removed ???
    {
        return $this->repository->getById($id);
    }

    /**
     * @param QuestionId $id
     * @return array
     * @throws DoctrineDBALException
     */
    public function getByIdWithDirectory(QuestionId $id): array
    {
        $question = $this->getById($id);
        $directory = $this->directoryComponent->getDirectoryForQuestion($id);
        $questionLevels = $this->relationRepository->getLevelsForQuestion($id);

        try {
            $questionId = $question->getId();
        } catch (NotFoundException $e) {
            throw new Exception('Question with ID ' . $id->toString() . ' not found', 400, $e);
        }

        $directoryRelations = [];
        $directoryList = [];
        try {
            /** @var DirectoryInterface $name */
            $name = $directory->offsetGet(0);
            $name = $name->getDirectoryName()->toString();
            $directoryRelations = [$questionId->toString() => $name];
            $directoryList = $directory->toList();
        } catch (NotFoundException $e) {
            if ($question->getType()->getValue() === QuestionTypeEnum::SELECT) {
                throw new Exception('Directory for question not found', 500);
            }
        }

        $levelIds = [];
        $levels = [];
        try {
            /** @var Level $level */
            foreach ($questionLevels as $level) {
                $levelId = $level->getId();
                $levelIds[] = $levelId->toInteger();
                $levels[$levelId->toInteger()] = $level->toArray();
            }
        } catch (NotFoundException $e) {
            // do nothing
        }
        $levelRelations[$questionId->toString()] = $levelIds;

        return [
            'questions' => $question->toArray(),
            'levels' => $levels,
            'directories' => $directoryList,
            'level_relations' => $levelRelations,
            'directory_relations' => $directoryRelations,
        ];
    }

    /**
     * @param LevelId $levelId
     * @return QuestionCollection
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getQuestionCollectionForLevel(LevelId $levelId): QuestionCollection
    {
        return $this->relationRepository->getQuestionsForLevel($levelId);
    }

    /**
     * @param QuestionId $questionId
     * @return LevelCollection
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getLevelCollectionForQuestion(QuestionId $questionId): LevelCollection
    {
        return $this->relationRepository->getLevelsForQuestion($questionId);
    }

    /**
     * @param LevelId $levelId
     * @return array
     * @throws DoctrineDBALException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getQuestionsForLevel(LevelId $levelId): array
    {
        $questions = $this->relationRepository->getQuestionsForLevel($levelId);

        $directoriesCollectionsArray = [];
        /** @var QuestionInterface $question */
        foreach ($questions as $question) {
            if ($question->getType()->getValue() === QuestionTypeEnum::SELECT) {
                $directoriesCollectionsArray[$question->getId()->toString()] = $this->directoryComponent->getDirectoryForQuestion($question->getId());
            }
        }
        $relations = [];
        $directories = [];
        foreach ($directoriesCollectionsArray as $questionId => $directoryCollection) {
            if ($directoryCollection->count()) {
                /** @var DirectoryInterface $directoryRow */
                $directoryRow = $directoryCollection->offsetGet(0);
                $directoryName = $directoryRow->getDirectoryName()->toString();
                $directories = array_merge($directories, $directoryCollection->toList());
                $relations[$questionId] = $directoryName;
            }
        }

        $result['questions'] = $questions->toArray();
        $result['directories'] = $directories;
        $result['directory_relations'] = $relations;

        return $result;
    }

    /**
     * @param CreateQuestionRequest $questionRequest
     * @return QuestionInterface
     * @throws DoctrineDBALDriverException
     * @throws DoctrineDBALException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function create(CreateQuestionRequest $questionRequest): QuestionInterface
    {
        $newQuestionId = new QuestionId($this->repository->getNewUuidId()->toString());
        $questionModel = new Question(
            $newQuestionId,
            $questionRequest->getQuestionTitle(),
            $questionRequest->getQuestionDescription(),
            $questionRequest->getSortOrder(),
            $questionRequest->getType(),
            $questionRequest->getStatus(),
            new QuestionMapper()
        );

        $directory = null;
        if ($questionRequest->getType()->getValue() === QuestionTypeEnum::SELECT) {
            $directoryCollection = $this->directoryComponent->getDirectoryByName($questionRequest->getDirectoryName());
            try {
                if (!$directoryCollection->count()) {
                    throw new NotFoundException('');
                }
            } catch (NotFoundException $e) {
                throw new Exception('Directory ' . $questionRequest->getDirectoryName()->toString() . ' not found', 400);
            }
            $directory = $directoryCollection->offsetGet(0);
        }

        $levels = [];
        /** @var LevelId $levelId */
        foreach ($questionRequest->getLevels() as $levelId) {
            $levels[$levelId->toInteger()] = $this->levelComponent->getLevelById($levelId);
        }
        foreach ($levels as $levelId => $level) {
            try {
                $level->getId();
            } catch (NotFoundException $e) {
                throw new Exception('Level with ID ' . $levelId . ' not found', 400);
            }
        }

        $question = $this->repository->create($questionModel);
        if ($directory) {
            $this->directoryComponent->attachDirectoryToQuestion($directory, $question);
        }
        foreach ($levels as $level) {
            $this->relationRepository->attachQuestionToLevel($question->getId(), $level->getId());
        }

        return $question;
    }

    /**
     * @param UpdateQuestionRequest $questionRequest
     * @return QuestionInterface
     * @throws DoctrineDBALException
     */
    public function update(UpdateQuestionRequest $questionRequest): QuestionInterface
    {
        $model = $this->repository->getById($questionRequest->getId());
        $currentLevels = $this->relationRepository->getLevelsForQuestion($questionRequest->getId());

        try {
            $questionModel = new Question(
                $model->getId(),
                $questionRequest->getQuestionTitle() ?? $model->getQuestionTitle(),
                $questionRequest->getQuestionDescription() ?? $model->getQuestionDescription(),
                $questionRequest->getSortOrder() ?? $model->getSortOrder(),
                $questionRequest->getType() ?? $model->getType(),
                $questionRequest->getStatus() ?? $model->getStatus(),
                $model->getMapper()
            );
        } catch (NotFoundException $e) {
            throw new Exception('Question not found', 400);
        }

        $directory = null;
        if ($questionModel->getType()->getValue() === QuestionTypeEnum::SELECT) {
            $directoryCollection = $this->directoryComponent->getDirectoryByName($questionRequest->getDirectoryName());
            if (!$directoryCollection->count()) {
                throw new Exception('Directory not found', 400);
            }
            $directory = $directoryCollection->offsetGet(0);
        }
        $newLevels = [];
        /** @var LevelId $levelId */
        foreach ($questionRequest->getLevelIdCollection() as $levelId) {
            $newLevels[$levelId->toInteger()] = $this->levelComponent->getLevelById($levelId);
        }
        foreach ($newLevels as $levelId => $newLevel) {
            try {
                $newLevel->getId();
            } catch (NotFoundException $e) {
                throw new Exception('Level with ID ' . $levelId . ' not found', 400);
            }
        }

        $question = $this->repository->update($questionModel);
        $this->directoryComponent->detachDirectoryFromQuestionId($questionRequest->getId());
        if ($directory) {
            $this->directoryComponent->attachDirectoryToQuestion($directory, $question);
        }
        /** @var Level $level */
        foreach ($currentLevels as $level) {
            $this->relationRepository->detachQuestionFromLevel($questionRequest->getId(), $level->getId());
        }
        /** @var LevelId $levelId */
        foreach ($newLevels as $level) {
            $this->relationRepository->attachQuestionToLevel($questionRequest->getId(), $level->getId());
        }

        return $question;
    }

    /**
     * @param QuestionId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(QuestionId $id): bool
    {
        $questionLevels = $this->relationRepository->getLevelsForQuestion($id);
        $this->directoryComponent->detachDirectoryFromQuestionId($id);
        /** @var Level $level */
        foreach ($questionLevels as $level) {
            $this->relationRepository->detachQuestionFromLevel($id, $level->getId());
        }
        return $this->repository->deleteById($id);
    }

    /**
     * @return array
     * @throws DoctrineDBALException
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Throwable
     */
    public function getAll(): array
    {
        /** @var QuestionCollection $questions */
        $questions = $this->repository->getAllQuestions();
        $levels = [];
        $levelRelations = [];
        $directoryRelations = [];

        $levelsForQuestion = [];
        $directoryFoQuestion = [];
        $questionsArray = [];

        /** @var Question $question */
        foreach ($questions as $question) {
            $questionId = $question->getId();
            $levelsForQuestion[$questionId->toString()] =
                $this->relationRepository->getLevelsForQuestion($questionId);


            $directoryFoQuestion[$questionId->toString()] =
                $this->directoryComponent->getDirectoryForQuestion($questionId);
            $questionsArray[] = $question->toArray();
        }

        foreach ($levelsForQuestion as $questionId => $collection) {
            $levelIds = [];
            /** @var Level $level */
            foreach ($collection as $level) {
                $levelId = $level->getId();
                $levelIds[] = $levelId->toInteger();
                $levels[$levelId->toInteger()] = $level->toArray();
            }
            $levelRelations[$questionId] = $levelIds;
        }

        $directories = [];
        foreach ($directoryFoQuestion as $questionId => $directoryCollection) {
            try {
                if ($directoryCollection->count()) {
                    /** @var DirectoryInterface $directoryRow */
                    $directoryRow = $directoryCollection->offsetGet(0);
                    $directoryName = $directoryRow->getDirectoryName()->toString();
                    $directories = array_merge($directories, $directoryCollection->toList());
                    $directoryRelations[$questionId] = $directoryName;
                }
            } catch (NotFoundException $e) {
                // skip questions without directories
            }

        }

        $result['questions'] = $questionsArray;
        $result['levels'] = $levels;
        $result['directories'] = $directories;
        $result['level_relations'] = $levelRelations;
        $result['directory_relations'] = $directoryRelations;

        return $result;
    }
}
