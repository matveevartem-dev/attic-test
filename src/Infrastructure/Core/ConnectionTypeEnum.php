<?php

declare(strict_types=1);

namespace App\Infrastructure\Core;

enum ConnectionTypeEnum: int
{
    /**
     * PDO connection
     */
    case PDO_MODE = 1;

    /**
     * Doctrine connection
    */
    case DOCTRINE_MODE = 2;
}
