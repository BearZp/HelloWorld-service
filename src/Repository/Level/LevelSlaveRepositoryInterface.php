<?php

namespace App\Repository\Level;

use App\DataType\LevelId;
use App\Model\Level\LevelCollection;
use App\Model\Level\LevelInterface;
use App\Repository\RepositoryInterface;
use Doctrine\DBAL\Exception as DoctrineDBALException;
use Tools\types\UuidType;

interface LevelSlaveRepositoryInterface extends RepositoryInterface
{
    /**
     * @return UuidType
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getNewUuidId(): UuidType;

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
     * @param LevelId $id
     * @return LevelInterface
     * @throws \Doctrine\DBAL\Exception
     */
    public function findParentLevel(LevelId $id): LevelInterface;

    /**
     * @return LevelCollection
     * @throws DoctrineDBALException
     */
    public function getAllLevelsByCriteria($params): LevelCollection;

    /**
     * @param LevelId $id
     * @return LevelInterface
     * @throws DoctrineDBALException
     */
    public function getById(LevelId $id): LevelInterface;
}