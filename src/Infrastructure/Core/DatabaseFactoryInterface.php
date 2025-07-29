<?php

declare(strict_types = 1);

namespace App\Infrastructure\Core;

use PDO;
use App\Infrastructure\Core\ConnectionTypeEnum;
use Doctrine\ORM\EntityManager;

interface DatabaseFactoryInterface
{
    /**
     * Creates a connection to the database
     * @param ConnectionTypeEnum $connectionType
     * @return EntityManager|PDO
     * @throws \Exception
     */
    public function create(ConnectionTypeEnum $connectionType): EntityManager|PDO;
}
