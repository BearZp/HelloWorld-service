<?php

namespace App\Component\Level;

use App\DataType\LevelId;
use App\Model\Level\LevelCollection;
use App\Model\Level\LevelInterface;
use App\Request\Level\CreateLevelRequest;
use App\Request\Level\UpdateLevelRequest;
use Doctrine\DBAL\Exception as DoctrineDBALException;

interface LevelComponentInterface
{
    /**
     * @return array
     * @throws DoctrineDBALException
     */
    public function getLevelsList(): array;

    /**
     * @return LevelCollection
     * @throws DoctrineDBALException
     */
    public function getAllLevels(): LevelCollection;

    /**
     * @param LevelId $id
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function getLevelById(LevelId $id): LevelInterface;

    /**
     * @param LevelId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteLevelById(LevelId $id): bool;

    /**
     * @param CreateLevelRequest $createLevelRequest
     * @return LevelInterface
     * @throws DoctrineDBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function createLevel(CreateLevelRequest $createLevelRequest): LevelInterface;

    /**
     * @param UpdateLevelRequest $updateLevelRequest
     * @return LevelInterface
     * @throws DoctrineDBALException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function updateLevel(UpdateLevelRequest $updateLevelRequest): LevelInterface;

    /**
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function getStartLevel(): LevelInterface;

    /**
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function getSecondLevel(): LevelInterface;

    /**
     * @param LevelId $id
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function findNextLevel(LevelId $id): LevelInterface;
}
