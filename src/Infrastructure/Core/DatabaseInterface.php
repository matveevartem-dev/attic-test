<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

use PDO;
use Doctrine\ORM\EntityManager;

interface DatabaseInterface
{
    /**
     * Returns a connection to the database
     * @return EntityManager|PDO
     */
    public function getConnecion(): EntityManager|PDO;
}