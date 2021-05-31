<?php

namespace App\Component\Directory;

use App\DataType\QuestionId;
use App\DataType\DirectoryId;
use App\DataType\DirectoryName;
use App\DataType\LevelId;
use App\Component\BaseComponent;
use App\Doctrine\Exception\NotFoundException;
use App\Model\Directory\Directory;
use App\Model\Directory\DirectoryCollection;
use App\Model\Directory\DirectoryInterface;
use App\Model\Question\QuestionInterface;
use App\Repository\Directory\DirectoryQuestionLevelRepositoryInterface;
use App\Repository\Directory\DirectoryRepositoryInterface;
use App\Request\Directory\CreateDirectoryRequest;
use App\Request\Directory\UpdateDirectoryRequest;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;
use Throwable;
use Psr\Cache\InvalidArgumentException;
use Tools\types\UuidType;

class DirectoryComponent extends BaseComponent implements DirectoryComponentInterface
{
    /** @var DirectoryRepositoryInterface */
    protected $repository;

    /** @var DirectoryQuestionLevelRepositoryInterface */
    protected $relationRepository;

    /**
     * DirectoryComponent constructor.
     * @param DirectoryRepositoryInterface $repository
     * @param DirectoryQuestionLevelRepositoryInterface $relationRepository
     */
    public function __construct(
        DirectoryRepositoryInterface $repository,
        DirectoryQuestionLevelRepositoryInterface $relationRepository
    ) {
        $this->repository = $repository;
        $this->relationRepository = $relationRepository;
    }

    /**
     * @param DirectoryId $id
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     */
    public function getById(DirectoryId $id): DirectoryInterface
    {
        return $this->repository->getById($id);
    }

    /**
     * @param DirectoryName $directoryName
     * @return DirectoryCollection
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function getDirectoryByName(DirectoryName $directoryName): DirectoryCollection
    {
        return $this->repository->getDirectoryByName($directoryName);
    }

    /**
     * @param QuestionId $questionId
     * @return DirectoryCollection
     * @throws InvalidArgumentException
     * @throws Throwable
     */
    public function getDirectoryForQuestion(QuestionId $questionId): DirectoryCollection
    {
//        try {
//            $directoryName = $this->relationRepository->getDirectoryNameForQuestionId($questionId);
//            $directory = $this->getDirectoryByName($directoryName);
//        } catch (NotFoundException $e) {
//            $directory = new DirectoryCollection([]);
//        }
//        return $directory;

        return $this->relationRepository->getDirectoriesForQuestionId($questionId);
    }

    /**
     * @param CreateDirectoryRequest $createDirectoryRequest
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(CreateDirectoryRequest $createDirectoryRequest): DirectoryInterface
    {
        return $this->repository->create($createDirectoryRequest);
    }

    /**
     * @param UpdateDirectoryRequest $updateDirectoryRequest
     * @return DirectoryInterface
     * @throws DoctrineDBALException
     */
    public function update(UpdateDirectoryRequest $updateDirectoryRequest): DirectoryInterface
    {
        $model = $this->repository->getById($updateDirectoryRequest->getId());

        $questionModel = new Directory(
            $model->getId(),
            $updateDirectoryRequest->getDirectoryName() ?? $model->getDirectoryName(),
            $updateDirectoryRequest->getDirectoryValue() ?? $model->getDirectoryValue(),
            $updateDirectoryRequest->getSortOrder() ?? $model->getSortOrder(),
            $updateDirectoryRequest->getStatus() ?? $model->getStatus(),
            $model->getMapper()
        );

        return $this->repository->update($questionModel);
    }

    /**
     * @param DirectoryId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(DirectoryId $id): bool
    {
        return $this->repository->deleteById($id);
    }

    /**
     * @return array
     * @throws DoctrineDBALException
     */
    public function getList(): array
    {
        return $this->repository->getAllDirectories()->toList();
    }

    /**
     * @param DirectoryInterface $directory
     * @param QuestionInterface $question
     * @return bool
     * @throws DoctrineDBALException
     */
    public function attachDirectoryToQuestion(DirectoryInterface $directory, QuestionInterface $question): bool
    {
        return $this->relationRepository->attachDirectoryToQuestion($question, $directory);
    }

    /**
     * @param QuestionId $questionId
     * @return bool
     * @throws DoctrineDBALException
     */
    public function detachDirectoryFromQuestionId(QuestionId $questionId): bool
    {
        return $this->relationRepository->detachDirectoryFromQuestionId($questionId);
    }
}
