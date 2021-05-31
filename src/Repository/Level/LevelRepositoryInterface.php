<?php

namespace App\Repository\Level;

use App\DataType\LevelId;
use App\Model\Level\LevelCollection;
use App\Model\Level\LevelInterface;
use App\Repository\RepositoryInterface;
use App\Request\Level\CreateLevelRequest;
use Doctrine\Common\Collections\Criteria;
use \Doctrine\DBAL\Exception as DoctrineDBALException;
use \Doctrine\DBAL\Driver\Exception as DoctrineDBALDriverException;

interface LevelRepositoryInterface extends RepositoryInterface
{
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
    public function getById(LevelId $id): LevelInterface;

    /**
     * @param LevelId $id
     * @return bool
     * @throws DoctrineDBALException
     */
    public function deleteById(LevelId $id): bool;

    /**
     * @param CreateLevelRequest $createLevelRequest
     * @return LevelInterface
     * @throws DoctrineDBALException
     * @throws DoctrineDBALDriverException
     */
    public function create(CreateLevelRequest $createLevelRequest): LevelInterface;

    /**
     * @param LevelInterface $level
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function update(LevelInterface $level): LevelInterface;
}
