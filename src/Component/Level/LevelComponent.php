<?php

namespace App\Component\Level;

use App\Component\BaseComponent;
use App\DataType\LevelId;
use App\Doctrine\Exception\NotFoundException;
use App\Model\Level\Level;
use App\Model\Level\LevelCollection;
use App\Model\Level\LevelInterface;
use App\Repository\Level\LevelRepositoryInterface;
use App\Repository\Level\LevelSlaveRepositoryInterface;
use App\Request\Level\CreateLevelRequest;
use App\Request\Level\UpdateLevelRequest;
use \Doctrine\DBAL\Exception as DoctrineDBALException;

class LevelComponent extends BaseComponent implements LevelComponentInterface
{
    /** @var LevelRepositoryInterface */
    protected $repository;
    /**
     * @var LevelSlaveRepositoryInterface
     */
    protected $slaveRepository;

    /**
     * LevelComponent constructor.
     * @param LevelRepositoryInterface $repository
     */
    public function __construct(LevelRepositoryInterface $repository, LevelSlaveRepositoryInterface $slaveRepository)
    {
        $this->repository = $repository;
        $this->slaveRepository = $slaveRepository;
    }

    /**
     * @return array
     * @throws DoctrineDBALException
     */
    public function getLevelsList(): array
    {
        $levels = [];
        foreach ($this->repository->getAllLevels() as $level) {
            $levels[$level->getId()->toString()] = $level->getName()->toString();
        }
        return $levels;
    }

    /**
     * @return LevelCollection
     * @throws DoctrineDBALException
     */
    public function getAllLevels(): LevelCollection
    {
        return $this->slaveRepository->getAllLevelsByCriteria([]);
    }

    /**
     * @param LevelId $id
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function getLevelById(LevelId $id): LevelInterface
    {
        return $this->repository->getById($id);
    }

    /**
     * @param LevelId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteLevelById(LevelId $id): bool
    {
        return $this->repository->deleteById($id);
    }

    /**
     * @param CreateLevelRequest $createLevelRequest
     * @return LevelInterface
     * @throws DoctrineDBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function createLevel(CreateLevelRequest $createLevelRequest): LevelInterface
    {
        return $this->repository->create($createLevelRequest);
    }

    /**
     * @param UpdateLevelRequest $updateLevelRequest
     * @return LevelInterface
     * @throws DoctrineDBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function updateLevel(UpdateLevelRequest $updateLevelRequest): LevelInterface
    {
        $level = $this->getLevelById($updateLevelRequest->getId());
        try {
            $levelId = $level->getId();
        } catch (NotFoundException $e) {
            throw new \Exception('Level with id ' . $updateLevelRequest->getId()->toString() . ' not found', 400);
        }
        $updatedLevel = new Level(
            $levelId,
            $updateLevelRequest->getName() ?? $level->getName(),
            $updateLevelRequest->getDescription() ?? $level->getDescription(),
            $updateLevelRequest->getAdvertisingText() ?? $level->getAdvertisingText(),
            $updateLevelRequest->getParentLevel() ?? $level->getParentLevel(),
            $updateLevelRequest->getIsFirst() ?? $level->getIsFirst(),
            $level->getMapper()
        );

        return $this->repository->update($updatedLevel);
    }

    /**
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function getStartLevel(): LevelInterface
    {
        return $this->repository->getStartLevel();
    }

    /**
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function getSecondLevel(): LevelInterface
    {
        return $this->repository->getSecondLevel();
    }

    /**
     * @param LevelId $id
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function findNextLevel(LevelId $id): LevelInterface
    {
        return $this->repository->findNextLevel($id);
    }

    /**
     * @param LevelId $id
     * @return LevelInterface
     */
    public function findParentLevel(LevelId $id): LevelInterface
    {
        return $this->repository->findParentLevel($id);
    }
}
