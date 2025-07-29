<?php

declare(strict_types = 1);

namespace App\Infrastructure\Core;

use PDO;
use PDOException;
use App\Infrastructure\Core\ConnectionTypeEnum;
use App\Infrastructure\Core\DatabaseFactoryInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

final class DatabaseFactory implements DatabaseFactoryInterface
{
    /**
     * @inheritDoc
     */
    public function create(ConnectionTypeEnum $connectionType = ConnectionTypeEnum::DOCTRINE_MODE): EntityManager|PDO {
        if ($connectionType === ConnectionTypeEnum::DOCTRINE_MODE) {
            return $this->createDoctrine();
        }

        if ($connectionType === ConnectionTypeEnum::PDO_MODE) {
            return $this->createPdo();
        }

        throw new \Exception();
    }

    /**
     * @return EntityManager
     */
    private function createDoctrine(): ?EntityManager
    {
        try {
            $config = ORMSetup::createAttributeMetadataConfig(
                paths: [__DIR__ . "/../../../src/Models"],
                isDevMode: true
            );
            $config->setProxyDir(sys_get_temp_dir());
            $config->setProxyNamespace("Proxy");

            $connection = DriverManager::getConnection([
                'driver'    => 'pdo_' . ($_ENV['DB_DRIBER'] ?? 'mysql'),
                'user'      => $_ENV['DB_USER'] ?? null,
                'password'  => $_ENV['DB_PASSWORD'] ?? null,
                'dbname'    => $_ENV['DB_NAME'] ?? '',
                'host'      => $_ENV['DB_HOST'] ?? 'localhost',
                'port'      => $_ENV['DB_PORT'] ?? '3306',
            ]);

            $em = new EntityManager($connection, $config);
        } catch (PDOException $e) {
            die($e->getMessage());
        }

        return $em;
    }

    /**
     * @return PDO
     */
    private function createPdo(): PDO
    {
        return 
            new PDO(
                $this->getDsn(),
                $_ENV['DB_USER'] ?? null,
                $_ENV['DB_PASSWORD'] ?? null,
                $_ENV['DB_OPTIONS'] ?? []
            );
    }

    /**
     * Returns DSN string
     * @return string
     */
    private function getDsn(): string
    {
        $driver = $_ENV['DB_DRIBER'] ?? 'mysql';
        $host = $_ENV['DB_HOST'] ?? 'localhost';
        $port = $_ENV['DB_PORT'] ?? $_ENV['DB_EXT_PORT'];
        $name = $_ENV['DB_NAME'] ?? '';
        $dsn = "{$driver}:host={$host}:{$port};dbname={$name}";

        return $dsn;
    }
}
