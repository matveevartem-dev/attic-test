<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

use PDO;
use Doctrine\ORM\EntityManager;
use App\Infrastructure\Core\ConnectionTypeEnum;
use App\Infrastructure\Core\DatabaseFactoryInterface;
use App\Infrastructure\Core\DatabaseInterface;

final class Database implements DatabaseInterface
{
    private $connection;

    public function __construct(private DatabaseFactoryInterface $dbFactory)
    {
        $this->connection ??= $dbFactory->create(ConnectionTypeEnum::PDO_MODE);
    }

    /**
     * @inheritDoc
     */
    public function getConnecion(): EntityManager|PDO
    {
        return $this->connection;
    }
}
